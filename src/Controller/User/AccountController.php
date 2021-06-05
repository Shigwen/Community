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
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
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
        $user = $this->getUser();
        $oldPass = $user->getPassword();
        $idCharacter = $request->query->get('id');

        if (!$character = $this->getDoctrine()->getRepository(Character::class)->findOneBy(['id' => $idCharacter])) {
            $character = new Character();
            $character
                ->setUser($this->getUser())
                ->setIsArchived(false);
        }

        if ($idCharacter && !$this->getUser()->hasCharacter($character)) {
            throw $this->createNotFoundException('Une erreur est survenue');
        }

        $formUser = $this->createForm(UserType::class, $user, [
            'isEdit' => true,
        ]);

        $formUser->handleRequest($request);
        if ($formUser->isSubmitted() && $formUser->isValid()) {
            $user = $formUser->getData();
            if (empty($user->getPassword())) {
                $encodedPass = $oldPass;
            } else {
                $encodedPass = $encoder->encodePassword($user, $user->getPassword());
            }

            $user->setPassword($encodedPass);
            $user->setUpdatedAt(new \DateTime());
            $this->getDoctrine()->getManager()->flush();
        }

        $formCharacter = $this->createForm(CharacterType::class, $character, [
            'isEdit' => $idCharacter ? true : false,
        ]);

        $formCharacter->handleRequest($request);
        if ($formCharacter->isSubmitted() && $formCharacter->isValid()) {
            $character = $formCharacter->getData();
            if (!$idCharacter) {
                $this->getDoctrine()->getManager()->persist($character);
            }
            $this->getDoctrine()->getManager()->flush();
        }

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
     * @Route("/{id}/archived", name="character_archived")
     */
    public function archived(Character $character): Response
    {
        if (!$this->getUser()->hasCharacter($character)) {
            throw new AccessDeniedHttpException();
        }

        $character->setIsArchived(true);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('user_account');
    }
}
