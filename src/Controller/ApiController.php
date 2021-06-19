<?php

namespace App\Controller;

use Error;
use DateTime;
use App\Entity\Raid;
use App\Entity\User;
use App\Form\UserType;
use App\Entity\Character;
use App\Service\Calendar;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/ajax", name="ajax")
 */
class ApiController extends AbstractController
{
    /**
     * @Route("/list-timezones-for/{country}")
     */
    public function getTimezones(string $country): Response
    {
        if (!$user = $this->getUser()) {
            $user = new User();
        }

        $form = $this->createForm(UserType::class, $user, [
            'country' => $country,
        ]);

        $html =  $this->render('api/_select_form_timezone.html.twig', [
            'form' => $form->createView(),
        ]);

        return new JsonResponse(['html' => $html->getContent()]);
    }

    /**
     * @Route("/get-availability-calendar")
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
     * @Route("/get-all-raid-of-the-day")
     */
    public function raidOfDay(Request $request): Response
    {
        $date = $request->request->get('date') ? new DateTime($request->request->get('date')) : null;
        $character = $this->getDoctrine()->getRepository(Character::class)->findOneBy(['id' => $request->request->get('character')]);
        $nbrOfResultPerPage = intval($request->request->get('numberOfResultPerPage'));
        $currentPage = intval($request->request->get('currentPage'));
        $offset = $currentPage * $nbrOfResultPerPage;

        if (!in_array($nbrOfResultPerPage, [10, 20, 50, 70, 100])) {
            throw new Error('Invalid request', 403);
        }

        if (!$date && !$character) {

            $nbrOfRaid = $this->getUser()
                ? $this->getDoctrine()->getRepository(Raid::class)->countAllRaidWhereUserIsAccepted($this->getUser())
                : $this->getDoctrine()->getRepository(Raid::class)->countAllPendingRaid();

            if ($nbrOfRaid) {
                $raids = $this->getUser()
                    ? $this->getDoctrine()->getRepository(Raid::class)->getAllRaidWhereUserIsAccepted($this->getUser(), $nbrOfResultPerPage, $offset)
                    : $this->getDoctrine()->getRepository(Raid::class)->getAllPendingRaid($nbrOfResultPerPage, $offset);
            } else {
                $raids = [];
            }
        } else if ($date && $character) {

            $nbrOfRaid = $this->getDoctrine()->getRepository(Raid::class)
                ->countAllRaidWhereUserCharacterIsAcceptedFromDate($this->getUser(), $character, $date);
            if ($nbrOfRaid) {
                $raids = $this->getDoctrine()->getRepository(Raid::class)
                    ->getAllRaidWhereUserCharacterIsAcceptedFromDate($this->getUser(), $character, $date, $nbrOfResultPerPage, $offset);
            } else {
                $raids = [];
            }
        } else {

            $nbrOfRaid = $this->getDoctrine()->getRepository(Raid::class)->countAllPendingRaidFromDate($date);
            if ($nbrOfRaid) {
                $raids = $this->getDoctrine()->getRepository(Raid::class)->getAllPendingRaidFromDate($date, $nbrOfResultPerPage, $offset);
            } else {
                $raids = [];
            }
        }

        if ($nbrOfRaid) {
            $nbrOfPages = intdiv($nbrOfRaid, $nbrOfResultPerPage);
            $nbrOfPages = ($nbrOfRaid % $nbrOfResultPerPage) ? $nbrOfPages : $nbrOfPages - 1;
        } else {
            $nbrOfPages = 0;
        }

        try {
            $html =  $this->renderView('event/event_list_parts/_raid_list.html.twig', [
                'nbrOfResultPerPage' => $nbrOfResultPerPage,
                'nbrOfPages' => $nbrOfPages,
                'currentPage' => $currentPage,
                'chosenDate' => $date,
                'character' => $character,
                'raids' => $raids,
            ]);
        } catch (Exception $e) {
            dd($e->getMessage());
        }

        return new Response($html);
    }
}
