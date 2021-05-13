<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home(): Response
    {
        return $this->render('home/home.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

	/**
     * @Route("/about-us", name="about_us")
     */
    public function aboutUs(): Response
    {
        return $this->render('home/about_us.html.twig');
    }

	/**
     * @Route("/terms-of-use-and-privacy-policy", name="terms_of_use")
     */
    public function termsOfUse(): Response
    {
        return $this->render('home/terms_of_use.html.twig');
    }
}
