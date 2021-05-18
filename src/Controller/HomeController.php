<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Service\Raid\Identifier;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class HomeController extends AbstractController
{
	/**
	 * Sign in
	 *
     * @Route("/", name="home")
     */
    public function signIn(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

		$user = new User();
		$form = $this->createForm(UserType::class, $user, [
			'action' => $this->generateUrl('sign_up'),
		]);

        return $this->render('home/home.html.twig', [
			'form' => $form->createView(),
			'last_username' => $lastUsername,
			'error' => $error,
		]);
    }

	/**
	 * Sign up
	 *
     * @Route("/sign-up", name="sign_up")
     */
    public function signUp(Request $request, UserPasswordEncoderInterface $passwordEncoder, Identifier $identifier, \Swift_Mailer $mailer): Response
    {
		$user = new User();
		$form = $this->createForm(UserType::class, $user);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$pwdEncoded = $passwordEncoder->encodePassword($user,$user->getPassword());
			$token = $identifier->generate(10);
			$url = $this->generateUrl('confirm_account', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

			$user = $form->getData();
			$user
				->setPassword($pwdEncoded)
				->setToken($token);


			$message = (new \Swift_Message('Validez votre inscription à Community'))
                ->setFrom('akmennra@gmail.com')
                ->setTo($user->getEmail())
                ->setBody(
					$this->renderView(
						'email/registration.html.twig',[
							'user'=> $user->getName(),
							'url'=> $url
						]
					),
                'text/html'
            );

            $mailer->send($message);

			$this->addFlash(
                'success',
                'Un email vient de vous être envoyé. Veuillez cliquer sur le lien qu\'il contient pour finaliser votre inscription.'
            );


			$this->getDoctrine()->getManager()->persist($user);
			$this->getDoctrine()->getManager()->flush();
		}

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/confirm-account/{token}", name="confirm_account")
     */
    public function confirmAccount(string $token)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->findOneBy([
			'token' => $token,
			'status' => User::STATUS_WAITING_EMAIL_CONFIRMATION,
			]);

        if (!$user){
            $this->addFlash('danger', 'Invalid token');
			return $this->redirectToRoute('login');
		}

		$user->setToken(null);
		$user->setStatus(User::STATUS_EMAIL_CONFIRMED);
		$entityManager->flush();

		$this->addFlash('success', 'Your account has been activated');

        return $this->redirectToRoute('home');
    }

	/**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
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
