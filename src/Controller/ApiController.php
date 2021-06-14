<?php

namespace App\Controller;

use App\Entity\Character;
use DateTime;
use App\Entity\Raid;
use App\Entity\Role;
use App\Entity\User;
use App\Form\UserType;
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

        try {
            if (!$date && $character) {
                $raids = $this->getDoctrine()->getRepository(Raid::class)->getAllRaidWhereUserIsAcceptedFromCharacter($this->getUser(), $character);
            } elseif ($date && $character) {
                $raids = $this->getDoctrine()->getRepository(Raid::class)
                    ->getAllRaidWhereUserCharacterIsAcceptedFromDate($this->getUser(), $character, $date);
            } else {
                $raids = $this->getDoctrine()->getRepository(Raid::class)->getAllPendingRaidFromDate($date);
            }
        } catch (Exception $e) {
            throw new Error($e->getMessage());
        }

        $html =  $this->renderView('event/_raid_list.html.twig', [
            'chosenDate' => $date,
            'character' => $character,
            'raids' => $raids,
        ]);

        return new Response($html);
    }
}
