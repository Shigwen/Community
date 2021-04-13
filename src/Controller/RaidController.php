<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RaidController extends AbstractController
{
    /**
     * @Route("/raid", name="raid")
     */
    public function index(): Response
    {
        return $this->render('raid/index.html.twig', [
            'controller_name' => 'RaidController',
        ]);
    }
}
