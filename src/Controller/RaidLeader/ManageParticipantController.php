<?php

namespace App\Controller\RaidLeader;

use App\Entity\User;
use App\Entity\RaidCharacter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @Route("/raid-leader/manage-players", name="raidleader_manage_players_")
 */
class ManageParticipantController extends AbstractController
{
    /**
     * @Route("/raid/character/{id}/accept-or-refuse/{acceptOrRefuse}", name="accept_or_refuse")
     */
    public function acceptOrRefuse(RaidCharacter $raidCharacter, int $acceptOrRefuse): Response
    {
        if (!in_array($acceptOrRefuse, [RaidCharacter::ACCEPT, RaidCharacter::REFUSED])) {
            throw new BadRequestHttpException('Bad status');
        }

        if ($acceptOrRefuse === RaidCharacter::ACCEPT) {
            $this->addFlash('success', $raidCharacter->getUserCharacter()->getName() .
                ' has been added to the raid roster');
        } else {
            $raidCharacterOfRaidLeader = $this->getDoctrine()->getRepository(RaidCharacter::class)->getOfRaidLeaderFromRaid(
                $raidCharacter->getRaid(),
            );

            if ($raidCharacterOfRaidLeader === $raidCharacter) {
                $this->addFlash('danger', 'You cannot refuse your own character in your own raid');
                return $this->redirectToRoute('raidleader_raid_manage_players', ['id' => $raidCharacterOfRaidLeader->getRaid()->getId()]);
            }

            $this->addFlash('success', $raidCharacter->getUserCharacter()->getName() .
                ' has been removed from the raid roster');
        }

        $raidCharacter->setStatus($acceptOrRefuse);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('raidleader_raid_manage_players', ['id' => $raidCharacter->getRaid()->getId()]);
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
        $currentRaid = $request->query->get('raid');

        if ($this->getUser() === $userToBan) {
            $this->addFlash('danger', 'You cannot ban yourself from your own raids');
            if ($currentRaid) {
                return $this->redirectToRoute('raidleader_raid_manage_players', ['id' => $currentRaid]);
            } else {
                return $this->redirectToRoute('raidleader_manage_players_ban_players');
            }
        }

        if ($raidLeader->hasBlocked($userToBan)) {
            $raidLeader->removeBlocked($userToBan);
            $this->addFlash('success', $userToBan->getName() . ' will be able to access your raids again');
        } else {
            $raidLeader->addBlocked($userToBan);

            foreach ($raidLeader->getRaids() as $raid) {
                $raidCharacter = $this->getDoctrine()->getRepository(RaidCharacter::class)
                    ->getOfUserFromRaid($raid, $userToBan);
                if ($raidCharacter) {
                    $raidCharacter->setStatus(RaidCharacter::REFUSED);
                }
            }

            $this->addFlash('success', $userToBan->getName() .
                ' has properly been banned from all your future raids (and kicked from current raids)');
        }

        $this->getDoctrine()->getManager()->flush();

        if ($currentRaid) {
            return $this->redirectToRoute('raidleader_raid_manage_players', ['id' => $currentRaid]);
        } else {
            return $this->redirectToRoute('raidleader_manage_players_ban_players');
        }
    }
}
