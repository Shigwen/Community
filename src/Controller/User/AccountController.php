<?php

namespace App\Controller\User;

use App\Entity\Raid;
use App\Entity\User;
use App\Form\UserType;
use App\Entity\Character;
use App\Form\CharacterType;
use App\Entity\RaidCharacter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
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
    public function account(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $user = $this->getUser();
        $oldPass = $user->getPassword();
        $idCharacter = $request->query->get('id');

        if (!$character = $this->getDoctrine()->getRepository(Character::class)->findOneBy(['id' => $idCharacter])) {
            $character = new Character();
            $character
                ->setUser($this->getUser())
                ->setIsArchived(false);
        } else {
            $gameVersion = $character->getServer()->getGameVersion();
        }

        if ($idCharacter && !$this->getUser()->hasCharacter($character)) {
            throw $this->createNotFoundException('Une erreur est survenue');
        }

        $formUser = $this->createForm(UserType::class, $user, [
            'isEdit' => true,
        ]);
        $formUser->get('country')->setData($user->getTimezone()->getCountry());

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

            $this->addFlash('success', 'Your personal informations have been properly modified');

            return $this->redirectToRoute('user_account');
        }

        $subscribedRaid = !$idCharacter ? [] : $this->getDoctrine()->getRepository(Raid::class)
            ->getPendingOrWaintingConfirmationRaidsOfCharacter($character);

        $formCharacter = $this->createForm(CharacterType::class, $character, [
            'isEdit' => $idCharacter ? true : false,
            'isSubscribeInARaid' => count($subscribedRaid),
        ]);

        if (count($subscribedRaid) == 0 && isset($gameVersion)) {
            $formCharacter->get('gameVersion')->setData($gameVersion->getId());
        }

        $formCharacter->handleRequest($request);
        if ($formCharacter->isSubmitted() && $formCharacter->isValid()) {
            $character = $formCharacter->getData();
            if (!$idCharacter) {
                $this->getDoctrine()->getManager()->persist($character);
            }
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Your character ' . $character->getName() . ' has been properly created');

            return $this->redirectToRoute('user_account');
        }

        if ($this->get('session')) {
            $this->get('session')->set('routeToRefer', 'user_account');
            $this->get('session')->set('nameOfPageToRefer', 'Back to account');
        }

        return $this->render('user/account.html.twig', [
            'formUser' => $formUser->createView(),
            'formCharacter' => $formCharacter->createView(),
            'character' => $idCharacter ? $character : null,
            'user' => $user,
            'characters' => $this->getDoctrine()->getRepository(Character::class)
                ->findBy(['user' => $user, 'isArchived' => false]),
            'forthcomingRaids' => $this->getDoctrine()->getRepository(Raid::class)
                ->getForthcomingRaidsOfPlayer($user, RaidCharacter::ACCEPT),
            'inProgressRaids' => $this->getDoctrine()->getRepository(Raid::class)
                ->getInProgressRaidsOfPlayer($user, RaidCharacter::ACCEPT),
            'waitOfConfirmationRaids' => $this->getDoctrine()->getRepository(Raid::class)
                ->getForthcomingRaidsOfPlayer($user, RaidCharacter::WAITING_CONFIRMATION),
            'refusedRaids' => $this->getDoctrine()->getRepository(Raid::class)
                ->getForthcomingRaidsOfPlayer($user, RaidCharacter::REFUSED),
            'archivedByRaidLeaderRaids' => $this->getDoctrine()->getRepository(Raid::class)
                ->getForthcomingArchivedByRaidLeader($user),
        ]);
    }

    /**
     * @Route("/delete-account", name="delete_account")
     */
    public function deleteAccount(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        // This own raid 
        foreach ($user->getRaids() as $raid) {
            foreach ($raid->getRaidCharacters() as $raidCharacter) {
                $this->getDoctrine()->getManager()->remove($raidCharacter);
            }
            $this->getDoctrine()->getManager()->remove($raid);
        }

        // Raid subscribe
        $raidCharacters = $this->getDoctrine()->getRepository(RaidCharacter::class)->getAllOfUser($user);
        foreach ($raidCharacters as $raidCharacter) {
            $this->getDoctrine()->getManager()->remove($raidCharacter);
        }

        // Characters
        foreach ($user->getCharacters() as $character) {
            $this->getDoctrine()->getManager()->remove($character);
        }

        // User blocked
        foreach ($user->getBlockeds() as $blocked) {
            $this->getDoctrine()->getManager()->remove($blocked);
        }

        $this->getDoctrine()->getManager()->remove($user);
        $this->getDoctrine()->getManager()->flush();

        // Remove session
        $session = $this->get('session');
        $session = new Session();
        $session->invalidate();

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/past-raid", name="raid_past")
     */
    public function past(): Response
    {
        return $this->render('raid_parts/past_raid_list.html.twig', [
            'raids' => $this->getDoctrine()->getRepository(Raid::class)
                ->getPastRaidsOfPlayer($this->getUser(), RaidCharacter::ACCEPT),
            'routeToRefer' => $this->get('session') ? $this->get('session')->get('routeToRefer') : null,
            'nameOfPageToRefer' => $this->get('session') ? $this->get('session')->get('nameOfPageToRefer') : null,
        ]);
    }

    /**
     * @Route("/{id}/archive", name="archive_character")
     */
    public function archiveCharacter(Character $character): Response
    {
        if (!$this->getUser()->hasCharacter($character)) {
            throw new AccessDeniedHttpException();
        }

        $raidForthcomingWhereUserIsRaidLeader = $this->getDoctrine()->getRepository(Raid::class)
            ->getForthcomingRaidsOfRaidLeader($this->getUser());

        $raidInProgressWhereUserIsRaidLeader = $this->getDoctrine()->getRepository(Raid::class)
            ->getInProgressRaidsOfRaidLeader($this->getUser());

        if (!empty($raidForthcomingWhereUserIsRaidLeader) || !empty($raidInProgressWhereUserIsRaidLeader)) {
            $this->addFlash('danger', "You cannot remove your raid leading characters from their raids");

            return $this->redirectToRoute('user_account');
        }

        $raidCharactersWhereCharacterIsNotRefused = $this->getDoctrine()->getRepository(RaidCharacter::class)
            ->getAllFutureRaidsNotRefusedWithCharacter($character);

        foreach ($raidCharactersWhereCharacterIsNotRefused as $raidCharacter) {
            $this->getDoctrine()->getManager()->remove($raidCharacter);
        }

        $character->setIsArchived(true);

        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('success', 'Your character ' . $character->getName() . ' has been properly deleted');

        return $this->redirectToRoute('user_account');
    }
}
