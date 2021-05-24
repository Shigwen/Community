<?php

namespace App\Controller\RaidLeader;

use DateTime;
use App\Entity\Raid;
use App\Form\RaidType;
use App\Entity\RaidTemplate;
use App\Entity\RaidCharacter;
use App\Service\Raid\Identifier;
use App\Service\Template\Template;
use App\Service\Raid\CreateRaidFromForm;
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
	 * Create a raid OR create a template OR edit a template
	 *
     * @Route("/add", name="add")
     */
    public function add(Request $request, Identifier $identifier, Template $template, CreateRaidFromForm $createRaidFromForm): Response
    {
        $raid = new Raid();
		$raid
			->setUser($this->getUser())
			->setIdentifier($identifier->generate(Raid::IDENTIFIER_SIZE));

		$raidCharacter = new RaidCharacter();
		$raidCharacter
			->setRaid($raid)
			->setStatus(RaidCharacter::ACCEPT);

		$raid->addRaidCharacter($raidCharacter);

		// Hydrate raid from the chosen template
        if ($raidTemplate = $this->getDoctrine()->getRepository(RaidTemplate::class)->findByIdAnduser(
			$request->query->get('id'),
			$this->getUser()
			)) {
            $raid = $template->hydrateRaidFromTemplate($raid, $raidTemplate);
        }

		$form = $this->createForm(RaidType::class, $raid, [
			'user' => $this->getUser(),
            'raidTemplate' => $raidTemplate,
		]);
		$form->handleRequest($request);

		// Create raid template
		if (!$raidTemplate && $form->get('saveTemplate')->isClicked() && $form->isValid()){
			$datas = $request->request->get('raid');
			$template->createOrEditTemplateFromRaid($datas['templateName'], $raid);
		}

		// Edit raid template
		if ($raidTemplate && $form->get('editTemplate')->isClicked() && $form->isValid()){
			$datas = $request->request->get('raid');
			$template->createOrEditTemplateFromRaid($datas['templateName'], $raid, $raidTemplate);
		}

		// Create raid
		if ($form->get('save')->isClicked() && $form->isValid()) {
			$createRaidFromForm->create($form, $raidCharacter, $this->getUser(), $request->request->get('raid'));
		}

       return $this->redirectToRoute('raidleader_events');
    }

	/**
     * @Route("/archived", name="archived")
     */
    public function archived(): Response
    {
		return $this->render('raid_leader/archived_raid_list.html.twig', [
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
     * @Route("/{id}/delete", name="delete")
     */
    public function delete(Raid $raid): Response
    {
		if (!$this->getUser()->hasRaid($raid)) {			
			throw new AccessDeniedHttpException();
		}

		$this->getDoctrine()->getManager()->remove($raid);
		$this->getDoctrine()->getManager()->flush();

		return $this->redirectToRoute('raidleader_events');
    }
}
