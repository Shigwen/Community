<?php

namespace App\Controller\RaidLeader;

use DateTime;
use App\Entity\Raid;
use App\Form\RaidType;
use App\Entity\RaidCharacter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Raid\RaidCharacterFromUserAndRaid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * @Route("/raid-leader/raid", name="raidleader_raid_")
 */
class RaidController extends AbstractController
{
    /**
     * @Route("/past", name="past")
     */
    public function past(): Response
    {
        return $this->render('raid_leader/past_raid_list.html.twig', [
            'raids' => $this->getDoctrine()->getRepository(Raid::class)->getPastRaidsOfRaidLeader($this->getUser()),
        ]);
    }

    /**
     * @Route("/{id}", name="show")
     */
    public function show(Raid $raid): Response
    {
        if (!$this->getUser()->hasRaid($raid)) {
            throw new AccessDeniedHttpException();
        }

        return $this->render('raid_leader/show_raid.html.twig', [
            'raid' => $raid,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit")
     */
    public function edit(Request $request, Raid $raid, RaidCharacterFromUserAndRaid $raidCharacterService): Response
    {
        if (!$this->getUser()->hasRaid($raid)) {
            throw new AccessDeniedHttpException();
        }

        $now = new DateTime();
        if ($now > $raid->getStartAt()) {
            $this->addFlash('danger', "You cannot modify a raid that already begun");
            return $this->redirectToRoute('raidleader_events');
        }

        $raidCharacterNotRefused = $this->getDoctrine()->getRepository(RaidCharacter::class)->getAllNotRefusedFromRaid($raid);

        // Show the form only if the raid haven't subscribe or in waiting players (except the raid leader himself)
        if (
            count($raidCharacterNotRefused) === 1 &&
            $raidCharacterNotRefused[0]->getUserCharacter()->getUser() === $this->getUser()
        ) {
            $form = $this->createForm(RaidType::class, $raid, [
                'user' => $this->getUser(),
                'isEdit' => true,
            ]);
            $form->get('raidCharacter')->setData($raidCharacterNotRefused[0]);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $raid = $form->getData();
                $raidLeaderCharacter = $raid->getCharacterFromUser($this->getUser());

                if (!$this->getUser()->hasCharacter($raidLeaderCharacter)) {
                    throw $this->createNotFoundException('Une erreur est survenue');
                }

                $raid
                    ->setUpdatedAt(new DateTime())
                    ->setServer($raidLeaderCharacter->getServer());

                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('raidleader_raid_edit', ['id' => $raid->getId()]);
            }
        }

        return $this->render('raid_leader/edit_raid.html.twig', [
            'user' => $this->getUser(),
            'raid' => $raid,
            'form' => isset($form) ? $form->createView() : null,
        ]);
    }

    /**
     * @Route("/{id}/archived", name="archived")
     */
    public function archived(Raid $raid): Response
    {
        if (!$this->getUser()->hasRaid($raid)) {
            throw new AccessDeniedHttpException();
        }

        $now = new DateTime();
        if ($now > $raid->getStartAt()) {
            $this->addFlash('danger', "You cannot delete a raid that already begun");
            return $this->redirectToRoute('raidleader_events');
        }

        $raid->setIsArchived(true);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('raidleader_events');
    }
}
