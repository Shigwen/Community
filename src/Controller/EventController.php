<?php

namespace App\Controller;

use DateTime;
use App\Entity\Raid;
use App\Entity\Role;
use App\Entity\Character;
use App\Service\Calendar;
use App\Entity\RaidCharacter;
use App\Form\RaidCharacterType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EventController extends AbstractController
{
    /**
     * @Route("/events", name="events")
     */
    public function eventList(Calendar $calendar): Response
    {
        $date = new DateTime();
        $month = $calendar::Process($date->format('Y-m-d'));
        $nbrOfResultPerPage = 2;

        $nbrRaids = $this->getUser()
            ? $this->getDoctrine()->getRepository(Raid::class)->countAllRaidWhereUserIsAccepted($this->getUser())
            : $this->getDoctrine()->getRepository(Raid::class)->countAllPendingRaid();

        $raids = $this->getUser()
            ? $this->getDoctrine()->getRepository(Raid::class)->getAllRaidWhereUserIsAccepted($this->getUser(), $nbrOfResultPerPage)
            : $this->getDoctrine()->getRepository(Raid::class)->getAllPendingRaid($nbrOfResultPerPage);

        $nbrPages = intdiv($nbrRaids, $nbrOfResultPerPage);
        $nbrPages = ($nbrRaids % $nbrOfResultPerPage) ? $nbrPages + 1 : $nbrPages;

        if ($user = $this->getUser()) {
            $raidCharacter = new RaidCharacter();
            $form = $this->createForm(RaidCharacterType::class, $raidCharacter, [
                'user' => $user,
            ]);
        }

        return $this->render('event/event_list.html.twig', [
            'raids' => $raids,
            'title' => $month['title'],
            'emptyDaysPadding' => $month['empty_days_padding'],
            'days' => $month['days'],
            'date' => $date,
            'nbrPages' => $nbrPages,
            'nbrOfResultPerPage' => $nbrOfResultPerPage,
            'form' => isset($form) ? $form->createView() : null
        ]);
    }

    /**
     * @Route("/event/{id}", name="event")
     */
    public function event(Request $request, Raid $raid): Response
    {
        if (!$this->getUser()) {
            return $this->render('event/show_event.html.twig', [
                'raid' => $raid,
                'tanks' => $this->getDoctrine()->getRepository(RaidCharacter::class)->getAllWithRole($raid, Role::TANK),
                'healers' => $this->getDoctrine()->getRepository(RaidCharacter::class)->getAllWithRole($raid, Role::HEAL),
                'dps' => $this->getDoctrine()->getRepository(RaidCharacter::class)->getAllWithRole($raid, Role::DPS),
            ]);
        }

        if (!$raidCharacter = $this->getDoctrine()->getRepository(RaidCharacter::class)->getOfUserFromRaid(
            $raid,
            $this->getUser()
        )) {
            $raidCharacter = new RaidCharacter();
            $raidCharacter
                ->setRaid($raid)
                ->setStatus($raid->isAutoAccept());
            $this->getDoctrine()->getManager()->persist($raidCharacter);
        }

        $raidCharacterFromRaidLeader = $this->getDoctrine()->getRepository(RaidCharacter::class)->getOfRaidLeaderFromRaid($raid);
        $characters = $this->getDoctrine()->getRepository(Character::class)->getAllByUserAndServerAndFaction(
            $this->getUser(),
            $raidCharacterFromRaidLeader->getUserCharacter()->getServer(),
            $raidCharacterFromRaidLeader->getUserCharacter()->getFaction()
        );

        if (count($characters) >= 1) {
            $form = $this->createForm(RaidCharacterType::class, $raidCharacter, [
                'user' => $this->getUser(),
                'raidCharacter' => $raidCharacterFromRaidLeader,
            ]);

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $raidCharacter = $form->getData();
                if ($raidCharacter->getUserCharacter()->getServer() !== $raidCharacterFromRaidLeader->getUserCharacter()->getServer()) {
                    $this->addFlash('danger', "Your character does not belong to the same server as the raid");
                    return $this->redirectToRoute('event', ['id' => $raid->getId()]);
                } else if ($raidCharacter->getUserCharacter()->getFaction() !== $raidCharacterFromRaidLeader->getUserCharacter()->getFaction()) {
                    $this->addFlash('danger', "Your character does not belong to the same faction as the raid");
                    return $this->redirectToRoute('event', ['id' => $raid->getId()]);
                }
                $this->getDoctrine()->getManager()->flush();
            }
        }

        return $this->render('event/show_event.html.twig', [
            'raid' => $raid,
            'tanks' => $this->getDoctrine()->getRepository(RaidCharacter::class)->getAllWithRole($raid, Role::TANK),
            'healers' => $this->getDoctrine()->getRepository(RaidCharacter::class)->getAllWithRole($raid, Role::HEAL),
            'dps' => $this->getDoctrine()->getRepository(RaidCharacter::class)->getAllWithRole($raid, Role::DPS),
            'user' => $this->getUser(),
            'form' => isset($form) ? $form->createView() : null,
            'isEdit' => $raidCharacter->getId(),
        ]);
    }

    /**
     * @Route("/{id}/unregister", name="unregister")
     */
    public function unregister(Request $request, Raid $raid): Response
    {
        $now = new DateTime();
        if ($now > $raid->getStartAt()) {
            $this->addFlash('danger', "You cannot unsubscribe from a raid that already begun");
            return $this->redirectToRoute('event', ['id' => $raid->getId()]);
        }

        $raidCharacter = $this->getDoctrine()->getRepository(RaidCharacter::class)->userAlreadyRegisterInRaid(
            $this->getUser(),
            $raid
        );

        $this->getDoctrine()->getManager()->remove($raidCharacter);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('event', ['id' => $raid->getId()]);
    }
}
