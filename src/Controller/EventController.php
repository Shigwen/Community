<?php

namespace App\Controller;

use App\Entity\Raid;
use App\Entity\RaidCharacter;
use App\Form\RaidCharacterType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EventController extends AbstractController
{
    /**
     * @Route("/events", name="events")
     */
    public function index(): Response
    {
        return $this->render('event/index.html.twig', [
            'raids' => $this->getDoctrine()->getRepository(Raid::class)->findBy([
				'isPrivate' => false,
			]),
        ]);
    }

	/**
     * @Route("/event/{id}/register", name="event_register")
     */
    public function register(Request $request, Raid $raid): Response
    {
		if (!$raidCharacter = $this->getDoctrine()->getRepository(RaidCharacter::class)->userAlreadyRegisterInRaid(
			$this->getUser(),
			$raid)
			) {
			$raidCharacter = new RaidCharacter();
			$raidCharacter
				->setRaid($raid)
				->setStatus($raid->getAutoAccept());
			$this->getDoctrine()->getManager()->persist($raidCharacter);
		}

		$form = $this->createForm(RaidCharacterType::class, $raidCharacter, [
			'user' => $this->getUser(),
		]);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$this->getDoctrine()->getManager()->flush();
			return $this->redirectToRoute('event', ['id' => $raid->getId()]);
		}

        return $this->render('event/show.html.twig', [
            'raid' => $raid,
			'charactersAccepted' => $this->getDoctrine()->getRepository(RaidCharacter::class)->findBy([
				'raid' => $raid,
				'status' => RaidCharacter::ACCEPT,
			]),
			'user' => $this->getUser(),
			'form' => $form->createView(),
        ]);
    }

	/**
     * @Route("/event/{id}/unregister", name="event_unregister")
     */
    public function unregister(Request $request, Raid $raid): Response
    {
		// Todo : on ne doit pas pouvoir supprimer le personnage
		// si c'est le personnage du créateur du raid !
		$raidCharacter = $this->getDoctrine()->getRepository(RaidCharacter::class)->userAlreadyRegisterInRaid(
			$this->getUser(),
			$raid
		);

		$this->getDoctrine()->getManager()->remove($raidCharacter);
		$this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('event', ['id' => $raid->getId()]);
    }
}
