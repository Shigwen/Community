<?php

namespace App\Controller\User;

use DateTime;
use App\Entity\Raid;
use App\Entity\RaidCharacter;
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
        // Todo : même page que les raid passé du raid leader (supprimer celle du raid leader)
        return $this->render('user/past_raid_list.html.twig', [
            'raids' => $this->getDoctrine()->getRepository(Raid::class)->getPastRaidsOfPlayer($this->getUser(), RaidCharacter::ACCEPT),
        ]);
    }

    /**
     * @Route("/{id}", name="show")
     */
    public function show(Raid $raid): Response
    {
        // Todo : même page que le show event (supprimer celle-ci ET celle du raid leader)
        return $this->render('user/show_raid.html.twig', [
            'raid' => $raid,
        ]);
    }

    /**
     * @Route("/{id}/unregister", name="unregister")
     */
    public function unregister(Request $request, Raid $raid): Response
    {
        // Todo : déplacer dans l'event controller
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
