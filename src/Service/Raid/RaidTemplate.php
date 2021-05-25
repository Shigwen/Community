<?php

namespace App\Service\Raid;

use App\Entity\Raid;
use App\Entity\RaidCharacter;
use App\Entity\User;
use App\Service\Raid\RaidRelation;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class RaidTemplate {

	/**
	 * @var EntityManagerInterface $em
	 */
	private $em;

	/**
	 * @var RaidRelation $raidService
	 */
	private $raidService;

	public function __construct(EntityManagerInterface $em, RaidRelation $raidService)
    {
		$this->em = $em;
		$this->raidService = $raidService;
    }

	public function createDefaultTemplate(User $user)
	{
		$date = new DateTime();
		$start = $date->setTime(16,0);
		$end = $date->setTime(20,0);

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
			->setIsPrivate(true)
			;

		$this->em->persist($raid);
		$this->em->flush();

		return $raid;
	}

	public function editTemplate(Raid $template, Raid $raidForm, RaidCharacter $raidCharacter, array $datas)
	{
		$template
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
			->setEndAt($raidForm->getEndAt())
			->setUpdatedAt(new DateTime());

		$template = $this->raidService->addCharacterAndServerToRaid($template, $raidCharacter, $datas);
		$this->em->flush();

		return $template;
	}

	public function calculationOfDateAndTimeOfRaid(Raid $raid)
	{
		$start = $raid->getStartAt();
		$end = $raid->getEndAt();
		$dayOfWeek = $raid->getVerboseStartDayOfWeek();

		$newStart = new DateTime();
		$newStart->modify($dayOfWeek.' this week');
		$newEnd = clone $newStart;

		$newStart->setTime(
			$start->format('H'),
			$start->format('i'),
			$start->format('s')
		);

		$newEnd->setTime(
			$end->format('H'),
			$end->format('i'),
			$end->format('s')
		);

		$raid
		->setStartAt($newStart)
		->setEndAt($newEnd);

		return $raid;
	}
}
