<?php

namespace App\Controller;

use DateTime;
use App\Entity\Raid;
use App\Service\Calendar;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CalendarController extends AbstractController
{
    /**
     * @Route("/ajax/get-availability-calendar")
     */
    public function calendar(Request $request, Calendar $calendar): Response
    {
        $date = new DateTime($request->request->get('date'));
		$month = $calendar::Process($date->format('Y-m-d'));

        $html =  $this->renderView('event/_calendar.html.twig', [
            'title' => $month['title'],
			'emptyDaysPadding' => $month['empty_days_padding'],
			'days' => $month['days'],
            'date' => $date,
        ]);

        return new Response($html);
    }

    /**
     * @Route("/ajax/get-all-raid-of-the-day")
     */
    public function raidOfDay(Request $request, Calendar $calendar): Response
    {
        $date = new DateTime($request->request->get('date'));
        
        $raids = $this->getUser()
		? $this->getDoctrine()->getRepository(Raid::class)->getAllRaidWhereUserIsAcceptedFromDate($this->getUser(), $date)
		: $this->getDoctrine()->getRepository(Raid::class)->getAllPendingRaidFromDate($date);

        $html =  $this->renderView('event/_raids.html.twig', [
            'chosenDate' => $date,
            'raids' => $raids,
        ]);

        return new Response($html);
    }
}
