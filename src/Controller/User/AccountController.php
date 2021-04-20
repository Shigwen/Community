<?php

namespace App\Controller\User;

use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user", name="user_")
 */
class AccountController extends AbstractController
{
    /**
     * @Route("/my-account", name="account")
     */
    public function index(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
		$user =  $user = $this->getUser();

        return $this->render('user/account/index.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/my-account/edit", name="account_edit")
     */
    public function editAccount(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
		$user =  $user = $this->getUser();
		$oldPass = $user->getPassword();

		$form = $this->createForm(UserType::class, $user);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();

            if (empty($user->getPassword())) {
                $encodedPass = $oldPass;
            } else {
                $encodedPass = $encoder->encodePassword($user, $user->getPassword());
            }

			$user->setPassword($encodedPass);
            $user->setUpdatedAt(new \DateTime());
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_account');
        }

        return $this->render('user/account/edit.html.twig', [
            'user' => $user,
			'form' => $form->createView(),
        ]);
    }
}
