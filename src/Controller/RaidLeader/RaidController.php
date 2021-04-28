<?php

namespace App\Controller\RaidLeader;

use DateTime;
use App\Entity\Raid;
use App\Entity\RaidCharacter;
use App\Form\RaidType;
use App\Service\Raid\Identifier;
use App\Service\Template\Template;
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
			'charactersAccepted' => $this->getDoctrine()->getRepository(RaidCharacter::class)->findBy([
				'raid' => $raid,
				'status' => RaidCharacter::ACCEPT,
			]),
			'charactersWaiting' => $this->getDoctrine()->getRepository(RaidCharacter::class)->findBy([
				'raid' => $raid,
				'status' => RaidCharacter::WAITING_CONFIRMATION,
			]),
        ]);
    }

    /**
     * @Route("/add", name="add")
     */
    public function add(Request $request, Identifier $identifier, Template $template): Response
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
			if(!$this->getUser()->hasCharacter($raidCharacter->getUserCharacter())) {
				throw $this->createNotFoundException('Une erreur est survenue');
			}

			$raid = $form->getData();
			$raid->setServer($raidCharacter->getCharacterServer());
			$datas = $request->request->get('raid');

			if($datas['templateName'] && $datas['dayOfWeek'] >= 1 && $datas['dayOfWeek'] <= 7) {
				$template->generateFromRaid($datas['templateName'], $raid, $datas['dayOfWeek'] );
			}

            $this->getDoctrine()->getManager()->persist($raid);
        	$this->getDoctrine()->getManager()->flush();

			return $this->redirectToRoute('user_account');
		}

        return $this->render('raid_leader/raid/action.html.twig', [
            'form' => $form->createView(),
			'nbrTemplate' => count($this->getUser()->getRaidTemplates()),
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
			'isEdit' => true,
		]);
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
