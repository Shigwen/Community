<?php

namespace App\Controller\RaidLeader;

use App\Entity\Raid;
use App\Form\RaidType;
use App\Entity\RaidCharacter;
use App\Service\Raid\Identifier;
use App\Service\Raid\RaidRelation;
use App\Service\Raid\RaidTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/raid-leader", name="raidleader_")
 */
class TemplateController extends AbstractController
{
    /**
     * @Route("/events", name="events")
     */
    public function events(Request $request, Identifier $identifier, RaidTemplate $template, RaidRelation $raidService): Response
    {
		$useTemplate = false;

		// Raid template existant ?
        if (!$raidTemplate = $this->getDoctrine()->getRepository(Raid::class)->getRaidTemplateByIdAndUser(
			$request->query->get('id'),
			$this->getUser()
			))
		{
			// Si non on créer un nouveau raid (en vue de devenir un template)
			$raidTemplate = new Raid();
			$raidCharacter = new RaidCharacter();
			$raidCharacter
			->setRaid($raidTemplate)
			->setStatus(RaidCharacter::ACCEPT);
			$raidTemplate
			->addRaidCharacter($raidCharacter)
			->setUser($this->getUser())
			->setIsArchived(false);
        } else {
			// Si oui on met à jour les dates et heures du raid
			$raidTemplate = $template->calculationOfDateAndTimeOfRaid($raidTemplate);
			$useTemplate = true;
			// Et on récupère le personnage/role enregistré
			if ($raidCharacter = $raidTemplate->getRaidCharacterFromUser($this->getUser())) {
				$character = $raidCharacter->getUserCharacter();
				$role = $raidCharacter->getRole();
			}
		}

		// Création du formulaire
		$form = $this->createForm(RaidType::class, $raidTemplate, [
			'user' => $this->getUser(),
			'raidInformation' => $raidTemplate->getInformation(),
            'isRaidTemplate' => $request->query->get('id') ? true: false,
		]);

		// Si on a le personnage/role on le set dans le form
		if ($useTemplate) {
			$raidCharacter = $raidTemplate->getRaidCharacterFromUser($this->getUser());
			$form->get('raidCharacter')->get('userCharacter')->setData($raidCharacter->getUserCharacter());
			$form->get('raidCharacter')->get('role')->setData($raidCharacter->getRole());
		}

		$allRaidTemplates = $this->getDoctrine()->getRepository(Raid::class)->getRaidTemplateByUser($this->getUser());

		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$raid = $raidService->addCharacterAndServerToRaid($raidTemplate, $raidCharacter, $request->request->get('raid'));

			if (!$useTemplate) {
				// Create new template
				if ($form->get('saveTemplate')->isClicked()) {
					if (count($allRaidTemplates) >= 5) {
						$this->addFlash('danger', "Oops, it seems that you've alreadu reached the maximum of templates allowed
						for this version of the app. Sorry ! Edit an old one you're not using,
						or delete one to free a slot in order to create a new one.");

						return $this->redirectToRoute('raidleader_events');
					}
					if (!$raidTemplate->getTemplateName()) {
						$raidTemplate->setTemplateName($raidTemplate->getName());
					}

				// Create new raid
				} else {
					$raidTemplate
					->setTemplateName(null)
					->setIdentifier($raidTemplate->getIsPrivate() ? $identifier->generate(Raid::IDENTIFIER_SIZE) : null);
				}

				$this->getDoctrine()->getManager()->persist($raid);

			} else {

				// Save chosen raid template as a new template
				if ($form->get('saveAsNewTemplate')->isClicked()) {
					if (count($allRaidTemplates) >= 5) {
						$this->addFlash('danger', "Oops, it seems that you've alreadu reached the maximum of templates allowed
						for this version of the app. Sorry ! Edit an old one you're not using,
						or delete one to free a slot in order to create a new one.");

						return $this->redirectToRoute('raidleader_events');
					}
					$newRaidTemplate = clone $raidTemplate;
					if (!$newRaidTemplate->getTemplateName()) {
						$newRaidTemplate->setTemplateName($newRaidTemplate->getName());
					}
					$this->getDoctrine()->getManager()->persist($newRaidTemplate);

				// Edit chosen raid template
				} else if ($form->get('editTemplate')->isClicked()) {
					if (!$raidTemplate->getTemplateName()) {
						$raidTemplate->setTemplateName($raidTemplate->getName());
					}

				// Create raid from the chosen raid template
				} else {
					$raid = clone $raidTemplate;
					$raid
					->setTemplateName(null)
					->setIdentifier($raid->getIsPrivate() ? $identifier->generate(Raid::IDENTIFIER_SIZE) : null);
					$this->getDoctrine()->getManager()->persist($raid);
				}
			}
			$this->getDoctrine()->getManager()->flush();
		}

		return $this->render('raid_leader/event_list.html.twig', [
			'user' => $this->getUser(),
			'pendingRaids' => $this->getDoctrine()->getRepository(Raid::class)->getPendingRaidsOfRaidLeader($this->getUser()),
			'inProgressRaids' => $this->getDoctrine()->getRepository(Raid::class)->getInProgressRaidsOfRaidLeader($this->getUser()),
			'raidTemplates' => $this->getDoctrine()->getRepository(Raid::class)->getRaidTemplateByUser($this->getUser()),
			'editTemplate' => $request->query->get('id') ? true: false,
			'form' => $form->createView(),
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
}
