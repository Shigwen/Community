<?php

namespace App\Controller\RaidLeader;

use DateTime;
use App\Entity\Raid;
use App\Form\RaidType;
use App\Entity\Character;
use App\Entity\RaidCharacter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("/raid-leader/raid", name="raidleader_raid_")
 */
class RaidController extends AbstractController
{
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

			return $this->redirectToRoute('raidleader_events');
		}

        return $this->render('raid_leader/raid/edit.html.twig', [
            'form' => $form->createView(),
			'raid' => $raid,
			'user' => $this->getUser(),
		]);
    }

	/**
     * @Route("/{raid_id}/manage-participant/{character_id}", name="manage_participant")
	 * @ParamConverter("raid", options={"id" = "raid_id"})
	 * @ParamConverter("character", options={"id" = "character_id"})
     */
    public function manageParticipant(Request $request, Raid $raid, Character $character): Response
    {
		$raidCharacter = $this->getDoctrine()->getManager()->getRepository(RaidCharacter::class)->findOneBy([
			'raid' => $raid,
			'userCharacter' => $character,
		]);

		if (!$raidCharacter) {
			throw $this->createNotFoundException('Une erreur est survenue');
		}

		$status = $request->query->get('status');
		if (!in_array($status, [RaidCharacter::ACCEPT, RaidCharacter::REFUSED])) {
			throw $this->createNotFoundException('Une erreur est survenue');
		}

		$raidCharacter->setStatus($status);
		$this->getDoctrine()->getManager()->flush();

		return $this->redirectToRoute('raidleader_raid_edit', ['id' => $raid->getId()]);
	}

	/**
     * @Route("/{id}/delete", name="delete")
     */
    public function delete(Raid $raid): Response
    {
		if ($this->getUser() && !$this->getUser()->hasRaid($raid)) {
			throw $this->createNotFoundException('Une erreur est survenue');
		}

		$this->getDoctrine()->getManager()->remove($raid);
		$this->getDoctrine()->getManager()->flush();

		return $this->redirectToRoute('raidleader_events');
    }
}
