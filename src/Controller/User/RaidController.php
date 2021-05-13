<?php

namespace App\Controller\User;

use App\Entity\Raid;
use App\Entity\RaidCharacter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/user/raid", name="user_raid_")
 */
class RaidController extends AbstractController
{
	/**
     * @Route("/archived", name="archived")
     */
    public function archived(): Response
    {
		return $this->render('user/archived_raid_list.html.twig', [
			'raids' => $this->getDoctrine()->getRepository(Raid::class)->getPastRaidsOfPlayer($this->getUser(), RaidCharacter::ACCEPT),
		]);
	}

	/**
     * @Route("/{id}", name="show")
     */
    public function show(Raid $raid): Response
    {
		return $this->render('user/show_raid.html.twig', [
			'raid' => $raid,
		]);
	}
}
