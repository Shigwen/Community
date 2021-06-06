<?php

namespace App\Controller\User;

use DateTime;
use App\Entity\Raid;
use App\Entity\RaidCharacter;
use App\Form\RaidCharacterType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/user/raid", name="user_raid_")
 */
class RaidController extends AbstractController
{
    /**
     * @Route("/past", name="past")
     */
    public function past(): Response
    {
        return $this->render('user/past_raid_list.html.twig', [
            'raids' => $this->getDoctrine()->getRepository(Raid::class)->getPastRaidsOfPlayer($this->getUser(), RaidCharacter::ACCEPT),
        ]);
    }

    /**
     * @Route("/{id}", name="show")
     */
    public function show(Raid $raid): Response
    {
        return $this->render('user/show_raid.html.twig', [
            'raid' => $raid,
        ]);
    }

    /**
     * @Route("/{id}/register", name="register")
     */
    public function register(Request $request, Raid $raid): Response
    {
        $now = new DateTime();
        if ($now > $raid->getStartAt()) {
            $this->addFlash('danger', "You cannot subscribe from a raid that already begun");
            return $this->redirectToRoute('user_raid_show', ['id' => $raid->getId()]);
        }

        if (!$raidCharacter = $this->getDoctrine()->getRepository(RaidCharacter::class)->userAlreadyRegisterInRaid(
            $this->getUser(),
            $raid
        )) {
            $raidCharacter = new RaidCharacter();
            $raidCharacter
                ->setRaid($raid)
                ->setStatus($raid->isAutoAccept());
            $this->getDoctrine()->getManager()->persist($raidCharacter);
        }

        $form = $this->createForm(RaidCharacterType::class, $raidCharacter, [
            'user' => $this->getUser(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
        }

        return $this->redirectToRoute('event', ['id' => $raid->getId()]);
    }

    /**
     * @Route("/{id}/unregister", name="unregister")
     */
    public function unregister(Request $request, Raid $raid): Response
    {
        $now = new DateTime();
        if ($now > $raid->getStartAt()) {
            $this->addFlash('danger', "You cannot unsubscribe from a raid that already begun");
            return $this->redirectToRoute('user_raid_show', ['id' => $raid->getId()]);
        }

        $raidCharacter = $this->getDoctrine()->getRepository(RaidCharacter::class)->userAlreadyRegisterInRaid(
            $this->getUser(),
            $raid
        );

        $this->getDoctrine()->getManager()->remove($raidCharacter);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('event', ['id' => $raid->getId()]);
    }
}
