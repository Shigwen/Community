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

        $status = intval($request->query->get('status'));
        if (!in_array($status, [RaidCharacter::ACCEPT, RaidCharacter::REFUSED])) {
            throw new BadRequestHttpException('Statut erroné');
        }

        if ($status === RaidCharacter::ACCEPT) {
            $this->addFlash('success', $raidCharacter->getUserCharacter()->getName() .
                ' has been added to the raid roster');
        } else {
            $this->addFlash('success', $raidCharacter->getUserCharacter()->getName() .
                ' has been removed from the raid roster');
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
        $currentRaid = $request->query->get('raid');

        if ($this->getUser() === $userToBan) {
            $this->addFlash('danger', 'Vous ne pouvez pas vous bannir de vos propres raids');
            if ($currentRaid) {
                return $this->redirectToRoute('raidleader_raid_edit', ['id' => $currentRaid]);
            } else {
                return $this->redirectToRoute('raidleader_manage_participant_ban_players');
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
            return $this->redirectToRoute('raidleader_raid_edit', ['id' => $currentRaid]);
        } else {
            return $this->redirectToRoute('raidleader_manage_participant_ban_players');
        }
    }
}
