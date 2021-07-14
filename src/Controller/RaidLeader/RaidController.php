<?php

namespace App\Controller\RaidLeader;

use DateTime;
use App\Entity\Raid;
use App\Form\RaidType;
use App\Entity\RaidCharacter;
use App\Entity\Role;
use App\Service\Raid\Identifier;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
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
        return $this->render('raid_parts/past_raid_list.html.twig', [
            'raids' => $this->getDoctrine()->getRepository(Raid::class)->getPastRaidsOfRaidLeader($this->getUser()),
            'user' => $this->getUser(),
            'routeToRefer' => $this->get('session') ? $this->get('session')->get('routeToRefer') : null,
            'nameOfPageToRefer' => $this->get('session') ? $this->get('session')->get('nameOfPageToRefer') : null,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit")
     */
    public function edit(Request $request, Raid $raid, Identifier $identifier): Response
    {
        if (!$this->getUser()->hasRaid($raid)) {
            throw new AccessDeniedHttpException();
        }

        if ($raid->getTemplateName()) {
            throw new BadRequestHttpException("This is not a raid");
        }

        $now = new DateTime();
        if ($now > $raid->getStartAt()) {
            $this->addFlash('danger', 'You cannot modify a raid that already begun');
            return $this->redirectToRoute('raidleader_events');
        }

        $form = $this->createForm(RaidType::class, $raid, [
            'user' => $this->getUser(),
            'isEdit' => true,
        ]);

        $raidCharacter = $this->getDoctrine()->getRepository(RaidCharacter::class)->getOfRaidLeaderFromRaid($raid);

        $form->get('raidCharacter')->setData($raidCharacter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $raid = $form->getData();
            $raidCharacter = $this->getDoctrine()->getRepository(RaidCharacter::class)->getOfRaidLeaderFromRaid($raid);

            if (!$this->getUser()->hasCharacter($raidCharacter->getUserCharacter())) {
                throw $this->createNotFoundException('Une erreur est survenue');
            }

            if (!$raid->isPrivate()) {
                $raid->setIdentifier(null);
            } else {
                $raid->setIdentifier($raid->getIdentifier() ? $raid->getIdentifier() : $identifier->generate(Raid::IDENTIFIER_SIZE));
            }

            $raid->setUpdatedAt(new DateTime());

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'The raid has been properly modified');

            return $this->redirectToRoute('raidleader_raid_edit', ['id' => $raid->getId()]);
        }

        return $this->render('raid_leader/edit_raid.html.twig', [
            'user' => $this->getUser(),
            'raid' => $raid,
            'editRaid' => true,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/manage-players", name="manage_players")
     */
    public function managePlayers(Raid $raid): Response
    {
        $now = new DateTime();
        if ($now > $raid->getStartAt()) {
            $this->addFlash('danger', 'You cannot modify a raid that already begun');
            return $this->redirectToRoute('raidleader_events');
        }

        return $this->render('raid_leader/manage_players.html.twig', [
            'raid' => $raid,
            'user' => $this->getUser(),
            'tanksWaitingConfirmation' => $this->getDoctrine()->getRepository(RaidCharacter::class)->findBy([
                'raid' => $raid,
                'status' => RaidCharacter::WAITING_CONFIRMATION,
                'role' => Role::TANK
            ]),
            'healersWaitingConfirmation' => $this->getDoctrine()->getRepository(RaidCharacter::class)->findBy([
                'raid' => $raid,
                'status' => RaidCharacter::WAITING_CONFIRMATION,
                'role' => Role::HEAL
            ]),
            'dpsWaitingConfirmation' => $this->getDoctrine()->getRepository(RaidCharacter::class)->findBy([
                'raid' => $raid,
                'status' => RaidCharacter::WAITING_CONFIRMATION,
                'role' => Role::DPS
            ]),
            'tanksValidated' => $this->getDoctrine()->getRepository(RaidCharacter::class)->findBy([
                'raid' => $raid,
                'status' => RaidCharacter::ACCEPT,
                'role' => Role::TANK
            ]),
            'healersValidated' => $this->getDoctrine()->getRepository(RaidCharacter::class)->findBy([
                'raid' => $raid,
                'status' => RaidCharacter::ACCEPT,
                'role' => Role::HEAL
            ]),
            'dpsValidated' => $this->getDoctrine()->getRepository(RaidCharacter::class)->findBy([
                'raid' => $raid,
                'status' => RaidCharacter::ACCEPT,
                'role' => Role::DPS
            ]),
            'tanksRefused' => $this->getDoctrine()->getRepository(RaidCharacter::class)->findBy([
                'raid' => $raid,
                'status' => RaidCharacter::REFUSED,
                'role' => Role::TANK
            ]),
            'healersRefused' => $this->getDoctrine()->getRepository(RaidCharacter::class)->findBy([
                'raid' => $raid,
                'status' => RaidCharacter::REFUSED,
                'role' => Role::HEAL
            ]),
            'dpsRefused' => $this->getDoctrine()->getRepository(RaidCharacter::class)->findBy([
                'raid' => $raid,
                'status' => RaidCharacter::REFUSED,
                'role' => Role::DPS
            ]),
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
        if ($now > $raid->getStartAt()) {
            $this->addFlash('danger', "You cannot delete a raid that already begun");
            return $this->redirectToRoute('raidleader_events');
        }

        $raid->setIsArchived(true);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('raidleader_events');
    }
}
