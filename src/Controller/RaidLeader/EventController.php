<?php

namespace App\Controller\RaidLeader;

use App\Entity\Raid;
use App\Form\RaidType;
use App\Entity\RaidCharacter;
use App\Service\Raid\RaidTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

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
    public function events(Request $request, RaidTemplate $template): Response
    {
        if (!$raid = $this->getDoctrine()->getRepository(Raid::class)->getRaidTemplateByIdAndUser(
			$request->query->get('id'),
			$this->getUser()
			))
		{
			$raid = new Raid();
			$raidCharacter = new RaidCharacter();
			$raid->addRaidCharacter($raidCharacter);
        } else {
			$raid = $template->calculationOfDateAndTimeOfRaid($raid);
			if ($raidCharacter = $raid->getRaidCharacterFromUser($this->getUser())) {
				$character = $raidCharacter->getUserCharacter();
				$role = $raidCharacter->getRole();
			}
		}

		$url = $request->query->get('id')
			? $this->generateUrl('raidleader_raid_add').'?id='.$request->query->get('id')
			: $this->generateUrl('raidleader_raid_add');

		$form = $this->createForm(RaidType::class, $raid, [
			'user' => $this->getUser(),
			'raidInformation' => $raid->getInformation(),
            'isRaidTemplate' => $request->query->get('id') ? true: false,
			'action' => $url,
		]);

		if (isset($character) && isset($role)) {
			$form->get('raidCharacter')->get('userCharacter')->setData($character);
			$form->get('raidCharacter')->get('role')->setData($role);
		}

		$raidTemplates = $this->getDoctrine()->getRepository(Raid::class)->getRaidTemplateByUser($this->getUser());

        return $this->render('raid_leader/event_list.html.twig', [
            'user' => $this->getUser(),
			'pendingRaids' => $this->getDoctrine()->getRepository(Raid::class)->getPendingRaidsOfRaidLeader($this->getUser()),
			'inProgressRaids' => $this->getDoctrine()->getRepository(Raid::class)->getInProgressRaidsOfRaidLeader($this->getUser()),
			'nbrTemplate' => count($raidTemplates),
			'raidTemplates' => $raidTemplates,
            'editTemplate' => $request->query->get('id') ? true: false,
            'form' => $form->createView(),
        ]);
    }

	/**
     * @Route("/template/{id}/delete", name="template_delete")
     */
    public function templateDelete(Raid $raid): Response
    {
		if ($this->getUser() && !$this->getUser()->hasRaid($raid)) {
			throw new AccessDeniedHttpException();
		}

		if (!$raid->getTemplateName()) {
			throw new BadRequestHttpException("Raid isn't a template");
		}

		$this->getDoctrine()->getManager()->remove($raid);
		$this->getDoctrine()->getManager()->flush();

		return $this->redirectToRoute('raidleader_events');
	}
}
