<?php

namespace App\Controller\RaidLeader;

use App\Entity\Raid;
use App\Entity\User;
use App\Entity\Character;
use App\Entity\RaidCharacter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("/raid-leader/manage-participant", name="raidleader_manage_participant_")
 */
class ManageParticipantController extends AbstractController
{
	/**
     * @Route("/{raid_id}/character/{character_id}/accept-or-refuse", name="accept_or_refuse")
	 * @ParamConverter("raid", options={"id" = "raid_id"})
	 * @ParamConverter("character", options={"id" = "character_id"})
     */
    public function acceptOrRefuse(Request $request, Raid $raid, Character $character): Response
    {
		$raidCharacter = $this->getDoctrine()->getRepository(RaidCharacter::class)->findOneBy([
			'raid' => $raid,
			'userCharacter' => $character,
		]);

		if (!$raidCharacter) {
			throw $this->createNotFoundException('Une erreur est survenue');
		}

		$status = $request->query->get('status');
		if (!in_array($status, [RaidCharacter::ACCEPT, RaidCharacter::REFUSED])) {
			throw new BadRequestHttpException('Statut erroné');
		}

		$raidCharacter->setStatus($status);
		$this->getDoctrine()->getManager()->flush();

		return $this->redirectToRoute('raidleader_raid_edit', ['id' => $raid->getId()]);
	}

	/**
     * @Route("/ban-players", name="ban_players")
     */
    public function banPlayers(): Response
    {
		return $this->render('raid_leader/ban_players.html.twig', [
            'user' => $this->getUser(),
        ]);
	}

	/**
     * @Route("/player/{id}/ban-hammer", name="ban_hammer")
     */
    public function banHammer(Request $request, User $userToBan): Response
    {
		$raidLeader = $this->getUser();
		$raid = $request->query->get('raid');

		if ($raidLeader->hasBlocked($userToBan)) {
			$raidLeader->removeBlocked($userToBan);
		} else {
			$raidLeader->addBlocked($userToBan);

			foreach ($raidLeader->getRaids() as $raid) {
				foreach ($raid->getRaidCharacters() as $character) {
					if ($character->getUser() === $userToBan) {
						$raidCharacter = $raid->getRaidCharacterFromUser($userToBan);
						$raidCharacter->setStatus(RaidCharacter::REFUSED);
					}
				}
			}
		}

		$this->getDoctrine()->getManager()->flush();

		if ($raid) {
			return $this->redirectToRoute('raidleader_raid_edit', ['id' => $raid]);
		} else {
			return $this->redirectToRoute('raidleader_manage_participant_ban_players');
		}
	}
}
