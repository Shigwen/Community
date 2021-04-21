<?php

namespace App\Controller\RaidLeader;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/raid-leader/raid", name="raidleader_raid_")
 */
class RaidController extends AbstractController
{
    /**
     * @Route("/add", name="add")
     */
    public function add(): Response
    {
        return $this->render('raid_leader/raid/index.html.twig', [
            'controller_name' => 'RaidController',
        ]);
    }

	/**
     * @Route("/edit", name="edit")
     */
    public function edit(): Response
    {
        return $this->render('raid_leader/raid/index.html.twig', [
            'controller_name' => 'RaidController',
        ]);
    }

	/**
     * @Route("/delete", name="delete")
     */
    public function delete(): Response
    {
        return $this->render('raid_leader/raid/index.html.twig', [
            'controller_name' => 'RaidController',
        ]);
    }
}
