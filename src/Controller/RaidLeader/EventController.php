<?php

namespace App\Controller\RaidLeader;

use App\Entity\Raid;
use App\Form\RaidType;
use App\Entity\RaidCharacter;
use App\Entity\RaidTemplate;
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
	 * Display the page with the list of template, the list of raids
	 * and the template creation or modification form
	 *
     * @Route("/events", name="events")
     */
    public function events(Request $request, Template $template): Response
    {
        $raid = new Raid();
		$raidCharacter = new RaidCharacter();
		$raid->addRaidCharacter($raidCharacter);

        if ($raidTemplate = $this->getDoctrine()->getRepository(RaidTemplate::class)->findByIdAnduser(
			$request->query->get('id'),
			$this->getUser()
			)) {
            $raid = $template->hydrateRaidFromTemplate($raid, $raidTemplate);
        }

		$url = $request->query->get('id')
			? $this->generateUrl('raidleader_raid_add').'?id='.$request->query->get('id')
			: $this->generateUrl('raidleader_raid_add');

		$form = $this->createForm(RaidType::class, $raid, [
			'user' => $this->getUser(),
            'raidTemplate' => $raidTemplate,
			'action' => $url,
		]);

        return $this->render('raid_leader/event_list.html.twig', [
            'user' => $this->getUser(),
			'pendingRaids' => $this->getDoctrine()->getRepository(Raid::class)->getPendingRaidsOfRaidLeader($this->getUser()),
			'inProgressRaids' => $this->getDoctrine()->getRepository(Raid::class)->getInProgressRaidsOfRaidLeader($this->getUser()),
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
		if ($this->getUser() && !$this->getUser()->hasRaidTemplate($raidTemplate)) {
			throw $this->createNotFoundException('Une erreur est survenue');
		}

		$this->getDoctrine()->getManager()->remove($raidTemplate);
		$this->getDoctrine()->getManager()->flush();

		return $this->redirectToRoute('raidleader_events');
	}
}
