<?php

namespace App\Controller\RaidLeader;

use DateTime;
use App\Entity\Raid;
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
        return $this->render('raid_leader/raid/show.html.twig', [
            'raid' => $raid,
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
		->setCreatedAt(new DateTime())
		->setIdentifier($identifier->generate(Raid::IDENTIFIER_SIZE));

		$form = $this->createForm(RaidType::class, $raid);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			$raid = $form->getData();
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

		$form = $this->createForm(RaidType::class, $raid);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			$raid = $form->getData();
			$raid->setUpdatedAt(new DateTime());
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
