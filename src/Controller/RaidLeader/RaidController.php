<?php

namespace App\Controller\RaidLeader;

use DateTime;
use App\Entity\Raid;
use App\Form\RaidType;
use App\Entity\RaidCharacter;
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
