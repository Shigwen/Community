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
     * @Route("/sign-up", name="sign_up")
     */
    public function signUp(Request $request, UserPasswordEncoderInterface $passwordEncoder, Identifier $identifier, \Swift_Mailer $mailer): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pwdEncoded = $passwordEncoder->encodePassword($user, $user->getPassword());
            $token = $identifier->generate(15);
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
                        'email/registration.html.twig',
                        [
                            'user' => $user->getName(),
                            'url' => $url
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
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([
            'token' => $token,
            'status' => User::STATUS_WAITING_EMAIL_CONFIRMATION,
        ]);

        if (!$user) {
            $this->addFlash('danger', 'Invalid token');
            return $this->redirectToRoute('home');
        }

        $user->setToken(null);
        $user->setStatus(User::STATUS_EMAIL_CONFIRMED);
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('success', 'Your account has been activated');

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/password-forgotten", name="password_forgotten")
     */
    public function passwordForgotten(Request $request, Identifier $identifier, \Swift_Mailer $mailer): Response
    {
        if ($email = $request->request->get('email')) {

            if (!$user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email])) {
                $this->addFlash('danger', 'No user registered with this email address');
                return $this->render('home/password_forgotten.html.twig');
            }

            $token = $identifier->generate(15, true, false);
            $user->setToken($token);
            $this->getDoctrine()->getManager()->flush();

            $url = $this->generateUrl('password_recovery', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
            $message = (new \Swift_Message('Mot de passe oublié - Community'))
                ->setFrom('akmennra@gmail.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        'email/password_forgotten.html.twig',
                        [
                            'user' => $user->getName(),
                            'url' => $url
                        ]
                    ),
                    'text/html'
                );

            $mailer->send($message);

            $this->addFlash(
                'success',
                'Un email vient de vous être envoyé. Veuillez cliquer sur le lien qu\'il contient pour changer votre mot de passe.'
            );


            return $this->redirectToRoute('home');
        }

        return $this->render('home/password_forgotten.html.twig');
    }

    /**
     * @Route("/password-recovery/{token}", name="password_recovery")
     */
    public function recoverPassword(Request $request, string $token, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $pass = $request->get('password');
        $passConfirm = $request->get('password_confirm');

        if (!$user = $this->getDoctrine()->getRepository(User::class)->findOneByToken($token)) {
            $this->addFlash('danger', 'Le lien servant à modifier ce mot de passe a déjà été utilisé');

            return $this->redirectToRoute('home');
        }

        if (!empty($pass) && !empty($passConfirm) && $pass === $passConfirm) {
            $user
                ->setToken(null)
                ->setPassword($passwordEncoder->encodePassword($user, $request->request->get('password')));

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Le mot de passe a bien été modifié');

            return $this->redirectToRoute('home');
        }

        return $this->render('home/password_recovery.html.twig');
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
