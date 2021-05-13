<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('home/home.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
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
