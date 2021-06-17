<?php

namespace App\Controller\RaidLeader;

use DateTime;
use App\Entity\Raid;
use App\Form\RaidType;
use App\Entity\RaidCharacter;
use App\Service\Raid\Identifier;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
        return $this->render('user_raid_leader_parts/past_raid_list.html.twig', [
            'raids' => $this->getDoctrine()->getRepository(Raid::class)->getPastRaidsOfRaidLeader($this->getUser()),
            'user' => $this->getUser(),
            'pathToRefer' => $this->get('session')->get('pathToRefer'),
            'nameOfPageToRefer' => $this->get('session')->get('nameOfPageToRefer'),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit")
     */
    public function edit(Request $request, Raid $raid, Identifier $identifier): Response
    {
        if (!$this->getUser()->hasRaid($raid)) {
            throw new AccessDeniedHttpException();
        }

        $now = new DateTime();
        if ($now > $raid->getStartAt()) {
            $this->addFlash('danger', "You cannot modify a raid that already begun");
            return $this->redirectToRoute('raidleader_events');
        }

        $form = $this->createForm(RaidType::class, $raid, [
            'user' => $this->getUser(),
            'isEdit' => true,
        ]);

        $raidCharacter = $this->getDoctrine()->getRepository(RaidCharacter::class)->getOfRaidLeaderFromRaid($raid);

        $form->get('raidCharacter')->setData($raidCharacter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $raid = $form->getData();
            $raidCharacter = $this->getDoctrine()->getRepository(RaidCharacter::class)->getOfRaidLeaderFromRaid($raid);

            if (!$this->getUser()->hasCharacter($raidCharacter->getUserCharacter())) {
                throw $this->createNotFoundException('Une erreur est survenue');
            }

            if ($raid->isPrivate()) {
            } else {
                $raid->setIdentifier(null);
            }

            $raid
                ->setIdentifier($raid->getIdentifier() ? $raid->getIdentifier() : $identifier->generate(Raid::IDENTIFIER_SIZE))
                ->setUpdatedAt(new DateTime());

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('raidleader_raid_edit', ['id' => $raid->getId()]);
        }

        return $this->render('raid_leader/edit_raid.html.twig', [
            'user' => $this->getUser(),
            'raid' => $raid,
            'form' => $form->createView(),
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
