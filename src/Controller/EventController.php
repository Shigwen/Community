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

        $raids = $this->getUser()
            ? $this->getDoctrine()->getRepository(Raid::class)->getAllRaidWhereUserIsAccepted($this->getUser())
            : $this->getDoctrine()->getRepository(Raid::class)->getAllPendingRaid();

        return $this->render('event/event_list.html.twig', [
            'raids' => $raids,
            'title' => $month['title'],
            'emptyDaysPadding' => $month['empty_days_padding'],
            'days' => $month['days'],
            'date' => $date,
        ]);
    }

    /**
     * @Route("/event/{id}", name="event")
     */
    public function event(Request $request, Raid $raid): Response
    {
        // Todo : ne pas oublier d'afficher l'identifier du raid si le raid appartient au raid leader
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
                    $this->addFlash('danger', "Votre personnage n'appartient pas au même serveur que le raid");
                    return $this->redirectToRoute('event', ['id' => $raid->getId()]);
                } else if ($raidCharacter->getUserCharacter()->getFaction() !== $raidCharacterFromRaidLeader->getUserCharacter()->getFaction()) {
                    $this->addFlash('danger', "Votre personnage n'appartient pas à la même faction que le raid");
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
}
