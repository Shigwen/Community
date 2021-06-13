<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

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
}
