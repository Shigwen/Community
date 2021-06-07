<?php

namespace App\Controller;

use DateTime;
use App\Entity\Raid;
use App\Entity\Role;
use App\Service\Calendar;
use App\Entity\RaidCharacter;
use App\Form\RaidCharacterType;
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
    public function event(Raid $raid): Response
    {
        if ($this->getUser()) {
            $isEdit = true;
            if (!$raidCharacter = $this->getDoctrine()->getRepository(RaidCharacter::class)->getOfUserFromRaid(
                $raid,
                $this->getUser()
            )) {
                $isEdit = false;
                $raidCharacter = new RaidCharacter();
            }

            // todo ajouter un filtre avec le server + la faction (pour le user qui s'inscrit à un raid)
            $form = $this->createForm(RaidCharacterType::class, $raidCharacter, [
                'user' => $this->getUser(),
                'action' => $this->generateUrl('user_raid_register', ['id' => $raid->getId()]),
            ]);
        }

        return $this->render('event/show_event.html.twig', [
            'raid' => $raid,
            'tanks' => $this->getDoctrine()->getRepository(RaidCharacter::class)->getAllWithRole($raid, Role::TANK),
            'healers' => $this->getDoctrine()->getRepository(RaidCharacter::class)->getAllWithRole($raid, Role::HEAL),
            'dps' => $this->getDoctrine()->getRepository(RaidCharacter::class)->getAllWithRole($raid, Role::DPS),
            'user' => $this->getUser() ? $this->getUser() : null,
            'form' => $this->getUser() ? $form->createView() : null,
            'isEdit' => $this->getUser() ? $isEdit : false,
        ]);
    }
}
