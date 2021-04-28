<?php

namespace App\Controller\RaidLeader;

use App\Entity\RaidTemplate;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/raid/leader/template", name="raid_leader_template_")
 */
class TemplateController extends AbstractController
{
    /**
     * @Route("/{id}", name="show", requirements={"id"="\d+"})
     */
    public function show(RaidTemplate $template): Response
    {
        return $this->render('raid_leader/template/index.html.twig', [
            'template' => $template,
        ]);
    }
}
