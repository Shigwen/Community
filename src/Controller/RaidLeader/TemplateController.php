<?php

namespace App\Controller\RaidLeader;

use App\Entity\Raid;
use App\Form\RaidType;
use App\Entity\RaidCharacter;
use App\Service\Raid\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * @Route("/raid-leader", name="raidleader_")
 */
class TemplateController extends AbstractController
{
    /**
     * @Route("/events", name="events")
     */
    public function events(Request $request, Template $templateService): Response
    {
        $newRaidTemplate = new Raid();
        $newRaidCharacter = new RaidCharacter();

        $newRaidCharacter
            ->setRaid($newRaidTemplate)
            ->setStatus(RaidCharacter::ACCEPT);
        $newRaidTemplate
            ->addRaidCharacter($newRaidCharacter)
            ->setUser($this->getUser())
            ->setIsArchived(false);

        if ($raidTemplateInUse = $this->getDoctrine()->getRepository(Raid::class)->getRaidTemplateByIdAndUser(
            $request->query->get('id'),
            $this->getUser()
        )) {
            $newRaidTemplate = clone $raidTemplateInUse;
            $newRaidTemplate = $templateService->calculateDateAndTimeFromTemplate($newRaidTemplate, $raidTemplateInUse);
        }

        $form = $this->createForm(RaidType::class, $newRaidTemplate, [
            'user' => $this->getUser(),
            'raidInformation' => $newRaidTemplate->getInformation(),
            'isRaidTemplate' => $raidTemplateInUse ? true : false,
        ]);

        if ($raidTemplateInUse) {
            $raidCharacter = $this->getDoctrine()->getRepository(RaidCharacter::class)->getOfRaidLeaderFromRaid($raidTemplateInUse);
            $form->get('raidCharacter')->get('userCharacter')->setData($raidCharacter->getUserCharacter());
            $form->get('raidCharacter')->get('role')->setData($raidCharacter->getRole());
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newRaidTemplate = $form->getData();
            $fragment = 'templateList';

            // Create new template
            if ($form->get('saveTemplate')->isClicked()) {
                $allTemplate = $this->getDoctrine()->getRepository(Raid::class)->getRaidTemplateByUser($this->getUser());

                if (count($allTemplate) < 5) {
                    $templateService->createTemplate($this->getUser(), $newRaidTemplate, $newRaidCharacter);
                    $this->addFlash('success', 'The raid template ' . $newRaidTemplate->getTemplateName() . ' has been properly created');
                } else {
                    $this->addFlash(
                        "danger",
                        "Oops, it seems that you've already reached the maximum of templates allowed for this version of the app. 
                         Sorry ! Edit an old one you're not using, or delete one to free a slot in order to create a new one"
                    );
                }

            // Edit chosen raid template
            } else if ($raidTemplateInUse && $form->get('editTemplate')->isClicked()) {
                $templateService->editChosenTemplate($raidTemplateInUse, $newRaidTemplate);
                $this->addFlash('success', 'The raid template ' . $newRaidTemplate->getTemplateName() . ' has been properly modified');

            // Create raid
            } else {
                $templateService->createRaid($newRaidTemplate, $newRaidCharacter);
                $this->addFlash('success', 'Your raid ' . $newRaidTemplate->getName() . ' has been properly created and published to the calendar');
                $fragment = 'raidList';
            }

            return $this->redirectToRoute('raidleader_events', ['_fragment' => $fragment]);
        }

        if ($this->get('session')) {
            $this->get('session')->set('routeToRefer', 'raidleader_events');
            $this->get('session')->set('nameOfPageToRefer', 'Back to HQ');
        }

        return $this->render('raid_leader/event_list.html.twig', [
            'user' => $this->getUser(),
            'raidTemplates' => $this->getDoctrine()->getRepository(Raid::class)->getRaidTemplateByUser($this->getUser()),
            'inProgressRaids' => $this->getDoctrine()->getRepository(Raid::class)->getInProgressRaidsOfRaidLeader($this->getUser()),
            'forthcomingRaids' => $this->getDoctrine()->getRepository(Raid::class)->getForthcomingRaidsOfRaidLeader($this->getUser()),
            'editTemplate' => $request->query->get('id') ? true : false,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/template/{id}/delete", name="template_delete")
     */
    public function templateDelete(Raid $raidTemplate): Response
    {
        if ($this->getUser() && !$this->getUser()->hasRaid($raidTemplate)) {
            throw new AccessDeniedHttpException();
        }

        if (!$raidTemplate->getTemplateName()) {
            throw new BadRequestHttpException("This is not a template");
        }

        $this->getDoctrine()->getManager()->remove($raidTemplate);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('raidleader_events');
    }
}
