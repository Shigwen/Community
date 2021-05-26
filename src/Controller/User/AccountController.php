<?php

namespace App\Controller\User;

use App\Entity\Raid;
use App\Form\UserType;
use App\Entity\Character;
use App\Form\CharacterType;
use App\Entity\RaidCharacter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user", name="user_")
 */
class AccountController extends AbstractController
{
    /**
     * @Route("/my-account", name="account")
     */
    public function index(Request $request): Response
    {
		$user = $this->getUser();
		$formUser = $this->createForm(UserType::class, $user, [
			'isEdit' => true,
			'action' => $this->generateUrl('user_account_edit'),
		]);

		$idCharacter = $request->query->get('id');
		if (!$character = $this->getDoctrine()->getRepository(Character::class)->findOneBy(['id' => $idCharacter])) {
			$character = new Character();
			$character->setUser($this->getUser());
		}

		if($idCharacter && !$this->getUser()->hasCharacter($character)) {
			throw $this->createNotFoundException('Une erreur est survenue');
		}

		$formCharacter = $this->createForm(CharacterType::class, $character, [
			'isEdit' => $idCharacter ? true : false,
			'action' => $idCharacter ? $this->generateUrl('user_character_edit', ['id' => $character->getId()]) : $this->generateUrl('user_character_add'),
		]);

        return $this->render('user/profil_page.html.twig', [
			'formUser' => $formUser->createView(),
			'formCharacter' => $formCharacter->createView(),
            'user' => $user,
			'characters' => $this->getDoctrine()->getRepository(Character::class)->findBy(['user' => $user, 'isArchived' => false]),
			'pendingRaids' => $this->getDoctrine()->getRepository(Raid::class)->getPendingRaidsOfPlayer($user, RaidCharacter::ACCEPT),
			'inProgressRaids' => $this->getDoctrine()->getRepository(Raid::class)->getInProgressRaidsOfPlayer($user, RaidCharacter::ACCEPT),
			'waitOfConfirmationRaids' => $this->getDoctrine()->getRepository(Raid::class)->getPendingRaidsOfPlayer($user, RaidCharacter::WAITING_CONFIRMATION),
        ]);
    }

    /**
     * @Route("/my-account/edit", name="account_edit")
     */
    public function editAccount(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
		$user = $this->getUser();
		$oldPass = $user->getPassword();

		$form = $this->createForm(UserType::class, $user, [
			'isEdit' => true,
		]);
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

        }

		return $this->redirectToRoute('user_account');
    }
}
