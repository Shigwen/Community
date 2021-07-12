<?php

namespace App\Controller;

use Error;
use DateTime;
use App\Entity\Raid;
use App\Entity\User;
use App\Form\UserType;
use App\Entity\Character;
use App\Entity\GameVersion;
use App\Form\CharacterType;
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
     * @Route("/list-servers-for-game-version/{id}")
     */
    public function getServers(Request $request, GameVersion $gameVersion): Response
    {
        if (!$character = $this->getDoctrine()->getRepository(Character::class)->findOneBy([
            'id' => $request->query->get('idCharacter')
        ])) {
            $character = new Character();
        }

        $form = $this->createForm(CharacterType::class, $character, [
            'gameVersion' => $gameVersion,
        ]);

        $html =  $this->render('api/_select_form_server.html.twig', [
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

        $html =  $this->renderView('event/parts/_calendar.html.twig', [
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
        $character = $this->getDoctrine()->getRepository(Character::class)->findOneBy(['id' => $request->request->get('character')]);
        $date = $request->request->get('date') ? new DateTime($request->request->get('date')) : null;

        $nbrOfResultPerPage = intval($request->request->get('numberOfResultPerPage'));
        $currentPage = intval($request->request->get('currentPage'));
        $offset = $currentPage * $nbrOfResultPerPage;

        if (!in_array($nbrOfResultPerPage, [10, 20, 50, 70, 100])) {
            throw new Error('Invalid request', 403);
        }

        try {
            $nbrOfRaid = $this->getDoctrine()->getRepository(Raid::class)->getAllPendingRaid(
                $this->getUser(),
                $character,
                $date
            );

            if ($nbrOfRaid) {
                $raids = $this->getDoctrine()->getRepository(Raid::class)->getAllPendingRaid(
                    $this->getUser(),
                    $character,
                    $date,
                    $nbrOfResultPerPage,
                    $offset
                );

                $nbrOfPages = intdiv($nbrOfRaid, $nbrOfResultPerPage);
                $nbrOfPages = ($nbrOfRaid % $nbrOfResultPerPage) ? $nbrOfPages : $nbrOfPages - 1;
            } else {
                $raids = [];
                $nbrOfPages = 0;
            }

            $html =  $this->renderView('event/parts/_raid_list.html.twig', [
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
