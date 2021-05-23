<?php

namespace App\Controller;

use App\Service\Calendar;
use DateTime;
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
			'empty_days_padding' => $month['empty_days_padding'],
			'days' => $month['days'],
            'date' => $date,
        ]);

        return new Response($html);
    }
}
