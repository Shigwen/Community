<?php

namespace App\Controller;

use DateTime;
use App\Entity\Raid;
use App\Entity\Role;
use App\Entity\Character;
use App\Service\Calendar;
use App\Entity\RaidCharacter;
use App\Form\RaidCharacterType;
use App\Service\Raid\NumberOfPlacesRemaining;
use App\Service\Raid\ReplacePlayer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EventController extends AbstractController
{
    /**
     * @Route("/events", name="events")
     */
    public function eventList(Request $request, Calendar $calendar): Response
    {
        $date = new DateTime();
        $month = $calendar::Process($date->format('Y-m-d'));
        $nbrOfResultPerPage = 10;

        $nbrOfRaid = $this->getDoctrine()->getRepository(Raid::class)->getAllPendingRaid(
            $this->getUser()
        );

        if ($nbrOfRaid) {
            $nbrOfPages = intdiv($nbrOfRaid, $nbrOfResultPerPage);
            $nbrOfPages = ($nbrOfRaid % $nbrOfResultPerPage) ? $nbrOfPages : $nbrOfPages - 1;
        } else {
            $nbrOfPages = 0;
        }

        if ($user = $this->getUser()) {
            $raidCharacter = new RaidCharacter();
            $form = $this->createForm(RaidCharacterType::class, $raidCharacter, [
                'user' => $user,
            ]);
        }

        $identifier = trim($request->query->get('identifier'));
        if ($identifier) {
            $raid = $this->getDoctrine()->getRepository(Raid::class)->findOneBy(['identifier' => $identifier]);
            if ($raid) {
                return $this->redirectToRoute('event', ['id' => $raid->getId()]);
            } else {
                $this->addFlash("danger", "This code doesn't match any raid");
            }
        }

        if ($this->get('session')) {
            $this->get('session')->set('routeToRefer', 'events');
            $this->get('session')->set('nameOfPageToRefer', 'Back to calendar');
        }

        return $this->render('event/event_list.html.twig', [
            'date' => $date,
            'title' => $month['title'],
            'days' => $month['days'],
            'emptyDaysPadding' => $month['empty_days_padding'],
            'raids' => [],
            'nbrOfPages' => $nbrOfPages,
            'nbrOfResultPerPage' => $nbrOfResultPerPage,
            'currentPage' => 0,
            'form' => isset($form) ? $form->createView() : null
        ]);
    }

    /**
     * @Route("/event/{id}", name="event")
     */
    public function event(Request $request, Raid $raid, NumberOfPlacesRemaining $nbrOfPlaces, ReplacePlayer $replacePlayer): Response
    {
        if (!$this->getUser()) {
            return $this->render('event/show_event.html.twig', [
                'raid' => $raid,
                'tanks' => $this->getDoctrine()->getRepository(RaidCharacter::class)->getAllWithRole($raid, Role::TANK),
                'healers' => $this->getDoctrine()->getRepository(RaidCharacter::class)->getAllWithRole($raid, Role::HEAL),
                'dps' => $this->getDoctrine()->getRepository(RaidCharacter::class)->getAllWithRole($raid, Role::DPS),
            ]);
        }

        if ($raidCharacter = $this->getDoctrine()->getRepository(RaidCharacter::class)->getOfUserFromRaid(
            $raid,
            $this->getUser()
        )) {
            if ($raidCharacter->getStatus() === RaidCharacter::ACCEPT) {
                $oldRole = $raidCharacter->getRole();
            }
        } else {
            $raidCharacter = new RaidCharacter();
            $raidCharacter->setRaid($raid);
            $this->getDoctrine()->getManager()->persist($raidCharacter);
        }

        $raidCharacterFromRaidLeader = $this->getDoctrine()->getRepository(RaidCharacter::class)->getOfRaidLeaderFromRaid($raid);
        $characters = $this->getDoctrine()->getRepository(Character::class)->getAllByUserAndServerAndFaction(
            $this->getUser(),
            $raidCharacterFromRaidLeader->getUserCharacter()->getServer(),
            $raidCharacterFromRaidLeader->getUserCharacter()->getFaction()
        );

        $now = new DateTime();
        $isPastRaid = $raid->getStartAt() <= $now;

        if (count($characters) >= 1 && !$isPastRaid && !$raidCharacter->isRefused()) {
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

                // Raid leader character
                if ($raidCharacter === $raidCharacterFromRaidLeader) {
                    $raidCharacter->setStatus(RaidCharacter::ACCEPT);
                    $this->addFlash('success', "You correctly subscribed your character to the raid");
                } else {
                    // Raid without Auto accept
                    if (!$raid->isAutoAccept()) {
                        $raidCharacter->setStatus(RaidCharacter::WAITING_CONFIRMATION);
                        $this->addFlash('success', "Your character is subscribed to the raid, and is now waiting for the raid leader to confirm the subscription");
                        // Raid with Auto accept
                    } else {
                        $status = $nbrOfPlaces->getStatusOfCharacterByPlacesRemaining($raid, $raidCharacter->getRole());
                        $raidCharacter->setStatus($status);

                        if ($status) {
                            $this->addFlash('success', "You correctly subscribed your character to the raid");
                        } else {
                            $this->addFlash('success', "Your character is subscribed to the raid, and is now waiting for the raid leader to confirm the subscription");
                        }

                        // The user change this role in the raid
                        if (isset($oldRole) && $oldRole !== $raidCharacter->getRole()) {
                            $replacePlayer->replace($raidCharacter, $oldRole);
                        }
                    }
                }

                $this->getDoctrine()->getManager()->flush();
                return $this->redirectToRoute('event', ['id' => $raid->getId()]);
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
            'isPastRaid' => $isPastRaid,
            'userIsRefused' => $raidCharacter->isRefused(),
            'routeToRefer' => $this->get('session') ? $this->get('session')->get('routeToRefer') : null,
            'nameOfPageToRefer' => $this->get('session') ? $this->get('session')->get('nameOfPageToRefer') : null,
        ]);
    }

    /**
     * @Route("/event/{id}/unregister", name="unregister")
     */
    public function unregister(Raid $raid, ReplacePlayer $replacePlayer): Response
    {
        $now = new DateTime();
        if ($now > $raid->getStartAt()) {
            $this->addFlash('danger', "You cannot unsubscribe from a raid that already begun");
            return $this->redirectToRoute('user_account');
        }

        $raidCharacterToUnsubscribe = $this->getDoctrine()->getRepository(RaidCharacter::class)->getOfUserFromRaid(
            $raid,
            $this->getUser()
        );

        $raidCharacterOfRaidLeader = $this->getDoctrine()->getRepository(RaidCharacter::class)->getOfRaidLeaderFromRaid(
            $raid
        );

        if ($raidCharacterOfRaidLeader === $raidCharacterToUnsubscribe) {
            $this->addFlash('danger', 'You cannot unsubscribe from your own raids');
            return $this->redirectToRoute('user_account');
        }

        if (!$raidCharacterToUnsubscribe->isRefused()) {
            $replacePlayer->replace($raidCharacterToUnsubscribe, $raidCharacterToUnsubscribe->getRole());

            $this->getDoctrine()->getManager()->remove($raidCharacterToUnsubscribe);
            $this->addFlash('success', "You successfully unsubscribed from the raid");
        } else {
            $this->addFlash('danger', "You cannot leave a raid from which your subscription has been rejected by the raid leader");
        }

        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('user_account');
    }
}
