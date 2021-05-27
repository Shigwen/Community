<?php

namespace App\Controller\RaidLeader;

use DateTime;
use App\Entity\Raid;
use App\Form\RaidType;
use App\Entity\RaidCharacter;
use App\Service\Raid\Identifier;
use App\Service\Raid\RaidTemplate;
use App\Service\Raid\RaidRelation;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * @Route("/raid-leader/raid", name="raidleader_raid_")
 */
class RaidController extends AbstractController
{
	/**
	 * Create a raid OR
	 * Create a template OR
	 * Edit a template
	 *
     * @Route("/add", name="add")
     */
    public function add(Request $request, Identifier $identifier, RaidTemplate $template, RaidRelation $raidService): Response
    {
        $raid = new Raid();
		$raid
		->setUser($this->getUser())
		->setIsArchived(false);

		$raidCharacter = new RaidCharacter();
		$raidCharacter
			->setRaid($raid)
			->setStatus(RaidCharacter::ACCEPT);

		$raid->addRaidCharacter($raidCharacter);

		$raidTemplate = $this->getDoctrine()->getRepository(Raid::class)->getRaidTemplateByIdAndUser(
			$request->query->get('id'),
			$this->getUser()
		);

		$form = $this->createForm(RaidType::class, $raid, [
			'user' => $this->getUser(),
            'isRaidTemplate' => $raidTemplate ? true : false,
		]);

		$form->handleRequest($request);

		$allRaidTemplates = $this->getDoctrine()->getRepository(Raid::class)->getRaidTemplateByUser($this->getUser());

		$startAt = $raid->getStartAt();
		$endAt = $raid->getEndAt();

		$endAt->setDate(
			$startAt->format('Y'),
			$startAt->format('m'),
			$startAt->format('d')
		);

		if (!$raidTemplate) {

			// Create new template
			if ($form->get('saveTemplate')->isClicked() && $form->isValid()) {
				if (count($allRaidTemplates) >= 5) {
					$this->addFlash('danger', "Oops, it seems that you've alreadu reached the maximum of templates allowed
					for this version of the app. Sorry ! Edit an old one you're not using,
					or delete one to free a slot in order to create a new one.");

					return $this->redirectToRoute('raidleader_events');
				}
				if (!$raid->getTemplateName()) {
					$raid->setTemplateName($raid->getName());
				}

			// Create new raid
			} else {
				$raid
				->setTemplateName(null)
				->setIdentifier($raid->getIsPrivate() ? $identifier->generate(Raid::IDENTIFIER_SIZE) : null);
			}

			$raid = $raidService->addCharacterAndServerToRaid($raid, $raidCharacter, $request->request->get('raid'));
			$this->getDoctrine()->getManager()->persist($raid);

		} else {

			// Save chosen raid template as a new template
			if ($form->get('saveAsNewTemplate')->isClicked() && $form->isValid()) {
				if (count($allRaidTemplates) >= 5) {
					$this->addFlash('danger', "Oops, it seems that you've alreadu reached the maximum of templates allowed
					for this version of the app. Sorry ! Edit an old one you're not using,
					or delete one to free a slot in order to create a new one.");

					return $this->redirectToRoute('raidleader_events');
				}
				if (!$raidTemplate->getTemplateName()) {
					$raidTemplate->setTemplateName($raidTemplate->getName());
				}
				$raid = $raidService->addCharacterAndServerToRaid($form->getData(), $raidCharacter, $request->request->get('raid'));
				$this->getDoctrine()->getManager()->persist($raid);

			// Edit chosen raid template
			} else if ($form->get('editTemplate')->isClicked() && $form->isValid() ) {
				if (!$raidTemplate->getTemplateName()) {
					$raidTemplate->setTemplateName($raidTemplate->getName());
				}
				$raidCharacter = $raidTemplate->getRaidCharacterFromUser($this->getUser());
				$raidTemplate = $template->editTemplate($raidTemplate, $raid, $raidCharacter, $request->request->get('raid'));

			// Create raid from the chosen raid template
			} else {
				$raid = $raidService->addCharacterAndServerToRaid($form->getData(), $raidCharacter, $request->request->get('raid'));
				$raid
				->setTemplateName(null)
				->setIdentifier($raid->getIsPrivate() ? $identifier->generate(Raid::IDENTIFIER_SIZE) : null);
				$this->getDoctrine()->getManager()->persist($raid);
			}
		}

		$this->getDoctrine()->getManager()->flush();

       return $this->redirectToRoute('raidleader_events');
    }

	/**
     * @Route("/past", name="past")
     */
    public function past(): Response
    {
		return $this->render('raid_leader/past_raid_list.html.twig', [
			'raids' => $this->getDoctrine()->getRepository(Raid::class)->getPastRaidsOfRaidLeader($this->getUser()),
		]);
	}

	/**
     * @Route("/{id}", name="show")
     */
    public function show(Raid $raid): Response
    {
		if (!$this->getUser()->hasRaid($raid)) {
			throw new AccessDeniedHttpException();
		}

		return $this->render('raid_leader/show_raid.html.twig', [
			'raid' => $raid,
		]);
	}

	/**
     * @Route("/{id}/edit", name="edit")
     */
    public function edit(Request $request, Raid $raid): Response
    {
		if (!$this->getUser()->hasRaid($raid)) {
			throw new AccessDeniedHttpException();
		}

		$now = new DateTime();
		if ($now > $raid->getStartAt() ) {
			$this->addFlash('danger', "You cannot modify a raid that already begun");
			return $this->redirectToRoute('raidleader_events');
		}

		if (!$raidCharacter = $raid->getRaidCharacterFromUser($this->getUser())) {
			$raidCharacter = new RaidCharacter();
			$raidCharacter
				->setStatus(RaidCharacter::ACCEPT)
				->setRaid($raid);
			$this->getDoctrine()->getManager()->persist($raidCharacter);
			$raid->addRaidCharacter($raidCharacter);
		}

		$form = $this->createForm(RaidType::class, $raid, [
			'user' => $this->getUser(),
			'isEdit' => true,
		]);
		$form->get('raidCharacter')->setData($raidCharacter);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$raid = $form->getData();
			$raidLeaderCharacter = $raid->getCharacterFromUser($this->getUser());

			if(!$this->getUser()->hasCharacter($raidLeaderCharacter)) {
				throw $this->createNotFoundException('Une erreur est survenue');
			}

			$raid
				->setUpdatedAt(new DateTime())
				->setServer($raidLeaderCharacter->getServer());

            $this->getDoctrine()->getManager()->flush();

			return $this->redirectToRoute('raidleader_raid_edit', ['id'=> $raid->getId()]);
		}

        return $this->render('raid_leader/edit_raid.html.twig', [
            'form' => $form->createView(),
			'raid' => $raid,
			'user' => $this->getUser(),
		]);
    }

	/**
     * @Route("/template/{id}/delete", name="template_delete")
     */
    public function templateDelete(Raid $raidTemplate): Response
    {
		if ($this->getUser() && !$this->getUser()->hasRaid($raidTemplate)) {
			throw new AccessDeniedHttpException();
		}

		if (!$raidTemplate->getTemplateName()) {
			throw new BadRequestHttpException("This is not a template");
		}

		$this->getDoctrine()->getManager()->remove($raidTemplate);
		$this->getDoctrine()->getManager()->flush();

		return $this->redirectToRoute('raidleader_events');
	}

	/**
     * @Route("/{id}/archived", name="archived")
     */
    public function archived(Raid $raid): Response
    {
		if (!$this->getUser()->hasRaid($raid)) {
			throw new AccessDeniedHttpException();
		}

		$now = new DateTime();
		if ($now > $raid->getStartAt() ) {
			$this->addFlash('danger', "You cannot delete a raid that already begun");
			return $this->redirectToRoute('raidleader_events');
		}

		$raid->setIsArchived(true);
		$this->getDoctrine()->getManager()->flush();

		return $this->redirectToRoute('raidleader_events');
    }
}
