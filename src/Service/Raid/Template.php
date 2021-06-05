<?php

namespace App\Service\Raid;

use DateTime;
use App\Entity\Raid;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Character;
use App\Entity\RaidCharacter;
use App\Service\Raid\Identifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class Template
{
    private $em;

    private $requestStack;

    private $identifier;

    public function __construct(
        EntityManagerInterface $em,
        RequestStack $requestStack,
        Identifier $identifier
    ) {
        $this->em = $em;
        $this->requestStack = $requestStack;
        $this->identifier = $identifier;
    }

    /**
     * Template automatically created when a user is promoted to raid leader
     */
    public function createDefaultTemplate(User $user)
    {
        $date = new DateTime();
        $start = $date->setTime(20, 0);
        $end = $date->setTime(23, 0);

        $raid = new Raid();
        $raid
            ->setName('Default')
            ->setTemplateName('Template default')
            ->setRaidType(25)
            ->setExpectedAttendee(24)
            ->setInformation('This is a default template, custom it and enjoy !')
            ->setMinTank(1)
            ->setMaxTank(5)
            ->setMinHeal(1)
            ->setMaxHeal(5)
            ->setUser($user)
            ->setStartAt($start)
            ->setEndAt($end)
            ->setAutoAccept(false)
            ->setIsPrivate(true);

        $this->em->persist($raid);
        $this->em->flush();

        return $raid;
    }

    /**
     * New raid registered as a template (from the raid creation form)
     */
    public function createTemplate(User $user, Raid $newRaidTemplate, RaidCharacter $newRaidCharacter)
    {
        $request = $this->requestStack->getCurrentRequest();
        $allRaidTemplates = $this->em->getRepository(Raid::class)->getRaidTemplateByUser($user);

        if (count($allRaidTemplates) >= 5) {
            $this->addFlash('danger', "Oops, it seems that you've alreadu reached the maximum of templates allowed
			for this version of the app. Sorry ! Edit an old one you're not using,
			or delete one to free a slot in order to create a new one.");

            return $this->redirectToRoute('raidleader_events');
        }

        if (!$newRaidTemplate->getTemplateName()) {
            $newRaidTemplate->setTemplateName($newRaidTemplate->getName());
        }

        $this->addCharacterAndServerToRaid($newRaidTemplate, $newRaidCharacter, $request->request->get('raid'));

        $this->em->persist($newRaidTemplate);
        $this->em->flush();
    }

    /**
     * When a user loads a template to fill the raid creation form, calculate the new dates (beginning/end)
     * for the raid, using the ones previously registered in the template.
     */
    public function calculateDateAndTimeFromTemplate(Raid $newRaid, Raid $raidTemplate)
    {
        $start = $raidTemplate->getStartAt();
        $end = $raidTemplate->getEndAt();
        $dayOfWeek = $raidTemplate->getVerboseStartDayOfWeek();

        $newStart = new DateTime();
        $newStart->modify($dayOfWeek . ' this week');
        $newStart->setTime(
            $start->format('H'),
            $start->format('i'),
            $start->format('s')
        );

        $now = new DateTime();
        if ($now > $newStart) {
            $newStart->modify($dayOfWeek . ' next week');
            $newStart->setTime(
                $start->format('H'),
                $start->format('i'),
                $start->format('s')
            );
        }

        $newEnd = clone $newStart;
        $newEnd->setTime(
            $end->format('H'),
            $end->format('i'),
            $end->format('s')
        );

        $newRaid
            ->setStartAt($newStart)
            ->setEndAt($newEnd);

        return $newRaid;
    }

    /**
     * Raid creation and publishing to the calendar (from the raid creation form)
     */
    public function createRaid(Raid $newRaid, RaidCharacter $newRaidCharacter)
    {
        $request = $this->requestStack->getCurrentRequest();

        $newRaid
            ->setTemplateName(null)
            ->setIdentifier($newRaid->getIsPrivate() ? $this->identifier->generate(Raid::IDENTIFIER_SIZE) : null);

        $this->addCharacterAndServerToRaid($newRaid, $newRaidCharacter, $request->request->get('raid'));

        $this->em->persist($newRaid);
        $this->em->flush();
    }

    /**
     * Modification of a template from the raid creation form (update the template using form data)
     */
    public function editChosenTemplate(User $user, Raid $raidTemplateInUse, Raid $raidForm)
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$raidTemplateInUse->getTemplateName()) {
            $raidTemplateInUse->setTemplateName($raidTemplateInUse->getName());
        }

        $raidTemplateInUse
            ->setName($raidForm->getName())
            ->setTemplateName($raidForm->getTemplateName())
            ->setRaidType($raidForm->getRaidType())
            ->setExpectedAttendee($raidForm->getExpectedAttendee())
            ->setInformation($raidForm->getInformation())
            ->setMinTank($raidForm->getMinTank())
            ->setMaxTank($raidForm->getMaxTank())
            ->setMinHeal($raidForm->getMinHeal())
            ->setMaxHeal($raidForm->getMaxHeal())
            ->setStartAt($raidForm->getStartAt())
            ->setUpdatedAt(new DateTime());

        $raidCharacter = $raidTemplateInUse->getRaidCharacterFromUser($user);
        $this->addCharacterAndServerToRaid($raidTemplateInUse, $raidCharacter, $request->request->get('raid'));

        $this->em->flush();
    }

    /**
     * Checking if a character role and a character are properly input in the raid creation form,
     * before linking them to an actual raid.
     */
    public function addCharacterAndServerToRaid(Raid $raid, RaidCharacter $raidCharacter, array $datas)
    {
        $character = $this->em->getRepository(Character::class)->findOneBy([
            'id' => $datas['raidCharacter']['userCharacter'],
            'user' => $raid->getUser(),
        ]);

        $role = $this->em->getRepository(Role::class)->findOneBy([
            'id' => $datas['raidCharacter']['role'],
        ]);

        if (!$character || !$role) {
            throw $this->createNotFoundException('Une erreur est survenue');
        }

        $raidCharacter
            ->setUserCharacter($character)
            ->setRole($role);

        $raid->setServer($character->getServer());

        if (!$raidCharacter->getId()) {
            $raid->addRaidCharacter($raidCharacter);
        }

        return $raid;
    }
}
