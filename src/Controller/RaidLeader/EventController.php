<?php

namespace App\Controller\RaidLeader;

use App\Entity\Raid;
use App\Form\RaidType;
use App\Entity\RaidCharacter;
use App\Entity\RaidTemplate;
use App\Service\Raid\Identifier;
use App\Service\Template\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/raid-leader", name="raidleader_")
 */
class EventController extends AbstractController
{
    /**
     * @Route("/events", name="events")
     */
    public function index(Request $request, Identifier $identifier, Template $template): Response
    {
        $raid = new Raid();
		$raid
			->setUser($this->getUser())
			->setIdentifier($identifier->generate(Raid::IDENTIFIER_SIZE));

		$raidCharacter = new RaidCharacter();
		$raidCharacter
			->setRaid($raid)
			->setStatus(RaidCharacter::ACCEPT);

		$raid->addRaidCharacter($raidCharacter);

        if($raidTemplate = $this->getDoctrine()->getManager()->getRepository(RaidTemplate::class)->findOneBy([
            'id'=> $request->query->get('id'),
        ])) {
            if(!$this->getUser()->hasRaidTemplate($raidTemplate)) {
				throw $this->createNotFoundException('Une erreur est survenue');
            }
            $raid = $template->hydrateRaidFromTemplate($raid, $raidTemplate);
        }

		$form = $this->createForm(RaidType::class, $raid, [
			'user' => $this->getUser(),
            'raidTemplate' => $raidTemplate,
		]);
		$form->handleRequest($request);

		if (!$raidTemplate && $form->get('saveTemplate')->isClicked() && $form->isValid()){
			$datas = $request->request->get('raid');
			$template->createOrHydrateTemplateFromRaid($datas['templateName'], $raid);
		}

		if ($raidTemplate && $form->get('editTemplate')->isClicked() && $form->isValid()){
			$datas = $request->request->get('raid');
			$template->createOrHydrateTemplateFromRaid($datas['templateName'], $raid, $raidTemplate);
		}

		if ($form->get('save')->isClicked() && $form->isValid()) {
			if(!$this->getUser()->hasCharacter($raidCharacter->getUserCharacter())) {
				throw $this->createNotFoundException('Une erreur est survenue');
			}

			$raid = $form->getData();
			$raid->setServer($raidCharacter->getCharacterServer());

            $this->getDoctrine()->getManager()->persist($raid);
        	$this->getDoctrine()->getManager()->flush();
		}

        return $this->render('raid_leader/event_list.html.twig', [
            'user' => $this->getUser(),
			'nbrTemplate' => count($this->getUser()->getRaidTemplates()),
            'editTemplate' => $raidTemplate ? true: false,
            'form' => $form->createView(),
        ]);
    }

	/**
     * @Route("/template/{id}/delete", name="template_delete")
     */
    public function templateDelete(RaidTemplate $raidTemplate): Response
    {
		if($this->getUser() && !$this->getUser()->hasRaidTemplate($raidTemplate)) {
			throw $this->createNotFoundException('Une erreur est survenue');
		}

		$this->getDoctrine()->getManager()->remove($raidTemplate);
		$this->getDoctrine()->getManager()->flush();

		return $this->redirectToRoute('raidleader_events');
	}
}
