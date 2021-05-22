<?php

namespace App\Service\Template;

use App\Entity\Raid;
use App\Entity\User;
use App\Entity\RaidTemplate;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class Template {

	/**
	 * @var EntityManagerInterface $em
	 */
	private $em;

	public function __construct(EntityManagerInterface $em)
    {
		$this->em = $em;
    }

	public function createDefaultTemplate(User $user)
	{
		$date = new DateTime();
		$start = $date->setTime(16,0);
		$end = $date->setTime(20,0);

		$template = new RaidTemplate();
		$template
			->setName('Template default')
			->setRaidType(25)
			->setExpectedAttendee(24)
			->setDayOfWeek(1)
			->setInformation('This is a default template, custom it and enjoy !')
			->setMinTank(1)
			->setMaxTank(5)
			->setMinHeal(1)
			->setMaxHeal(5)
			->setUser($user)
			->setStartAt($start)
			->setEndAt($end);

		$this->em->persist($template);
		$this->em->flush();

		return $template;
	}

	public function createOrEditTemplateFromRaid($templateName, Raid $raid, RaidTemplate $template = null)
	{
		if (!$template) {
			$template = new RaidTemplate();
			$this->em->persist($template);
		}

		$template
			->setName($templateName)
			->setRaidType($raid->getRaidType())
			->setExpectedAttendee($raid->getExpectedAttendee())
			->setDayOfWeek($raid->getRaidDayOfWeek())
			->setInformation($raid->getInformation())
			->setMinTank($raid->getMinTank())
			->setMaxTank($raid->getMaxTank())
			->setMinHeal($raid->getMinHeal())
			->setMaxHeal($raid->getMaxHeal())
			->setUser($raid->getUser())
			->setStartAt($raid->getStartAt())
			->setEndAt($raid->getEndAt());

			$this->em->flush();

			return $template;
	}

	public function hydrateRaidFromTemplate(Raid $raid, RaidTemplate $raidTemplate)
	{
		$raid
			->setRaidType($raidTemplate->getRaidType())
			->setExpectedAttendee($raidTemplate->getExpectedAttendee())
			->setInformation($raidTemplate->getInformation())
			->setMinTank($raidTemplate->getMinTank())
			->setMaxTank($raidTemplate->getMaxTank())
			->setMinHeal($raidTemplate->getMinHeal())
			->setMaxHeal($raidTemplate->getMaxHeal())
			->setStartAt($raidTemplate->getStartAt())
			->setEndAt($raidTemplate->getEndAt());

			return $raid;
	}
}
