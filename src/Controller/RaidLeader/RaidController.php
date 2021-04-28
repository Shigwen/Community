<?php

namespace App\Controller\RaidLeader;

use DateTime;
use App\Entity\Raid;
use App\Entity\RaidCharacter;
use App\Form\RaidType;
use App\Service\Raid\Identifier;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/raid-leader/raid", name="raidleader_raid_")
 */
class RaidController extends AbstractController
{
	/**
     * @Route("/{id}", name="show", requirements={"id"="\d+"})
     */
    public function show(Raid $raid): Response
    {
		$charactersAccepted = $this->getDoctrine()->getRepository(RaidCharacter::class)->findBy([
			'raid' => $raid,
			'status' => RaidCharacter::ACCEPT,
		]);
		$charactersWaiting = $this->getDoctrine()->getRepository(RaidCharacter::class)->findBy([
			'raid' => $raid,
			'status' => RaidCharacter::WAITING_CONFIRMATION,
		]);

        return $this->render('raid_leader/raid/show.html.twig', [
            'raid' => $raid,
			'charactersAccepted' => $charactersAccepted,
			'charactersWaiting' => $charactersWaiting,
        ]);
    }

    /**
     * @Route("/add", name="add")
     */
    public function add(Request $request, Identifier $identifier): Response
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

		$form = $this->createForm(RaidType::class, $raid, [
			'user' => $this->getUser(),
		]);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$raid = $form->getData();
			$raid->setServer($raidCharacter->getCharacterServer());

			if(!$this->getUser()->hasCharacter($raidCharacter->getUserCharacter())) {
				throw $this->createNotFoundException('Une erreur est survenue');
			}

            $this->getDoctrine()->getManager()->persist($raid);
        	$this->getDoctrine()->getManager()->flush();

			return $this->redirectToRoute('user_account');
		}

        return $this->render('raid_leader/raid/action.html.twig', [
            'form' => $form->createView(),
        ]);
    }

	/**
     * @Route("/{id}/edit", name="edit")
     */
    public function edit(Request $request, Raid $raid): Response
    {
		if($this->getUser() && !$this->getUser()->hasRaid($raid)) {
			throw $this->createNotFoundException('Une erreur est survenue');
		}

		$form = $this->createForm(RaidType::class, $raid, [
			'user' => $this->getUser(),
		]);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			$raid = $form->getData();
			$raid->setUpdatedAt(new DateTime());
			$raidLeaderCharacter = $raid->getCharacterFromUser($this->getUser());
			$raid->setServer($raidLeaderCharacter->getServer());

			if(!$this->getUser()->hasCharacter($raidLeaderCharacter)) {
				throw $this->createNotFoundException('Une erreur est survenue');
			}

            $this->getDoctrine()->getManager()->flush();

			return $this->redirectToRoute('user_account');
		}

        return $this->render('raid_leader/raid/action.html.twig', [
            'form' => $form->createView(),
        ]);
    }

	/**
     * @Route("/{id}/delete", name="delete")
     */
    public function delete(Raid $raid): Response
    {
		if($this->getUser() && !$this->getUser()->hasRaid($raid)) {
			throw $this->createNotFoundException('Une erreur est survenue');
		}

		$this->getDoctrine()->getManager()->remove($raid);
		$this->getDoctrine()->getManager()->flush();

		return $this->redirectToRoute('user_account');
    }
}
