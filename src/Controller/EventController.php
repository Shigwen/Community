<?php

namespace App\Controller;

use App\Entity\Raid;
use App\Entity\RaidCharacter;
use App\Form\RaidCharacterType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EventController extends AbstractController
{
    /**
     * @Route("/events", name="events")
     */
    public function eventList(): Response
    {
        return $this->render('event/event_list.html.twig', [
            'raids' => $this->getDoctrine()->getRepository(Raid::class)->findBy([
				'isPrivate' => false,
			]),
        ]);
    }

	/**
     * @Route("/event/{id}", name="event")
     */
    public function event(Raid $raid): Response
    {
		$isEdit = true;
		if (!$raidCharacter = $this->getDoctrine()->getRepository(RaidCharacter::class)->userAlreadyRegisterInRaid(
			$this->getUser(),
			$raid)
		) {
			$isEdit = false;
			$raidCharacter = new RaidCharacter();
		}

		$form = $this->createForm(RaidCharacterType::class, $raidCharacter, [
			'user' => $this->getUser(),
			'action' => $this->generateUrl('user_raid_register', ['id' => $raid->getId()]),
		]);

        return $this->render('event/show_event.html.twig', [
            'raid' => $raid,
			'user' => $this->getUser(),
			'form' => $form->createView(),
			'isEdit' => $isEdit,
        ]);
    }
}
