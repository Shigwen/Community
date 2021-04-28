<?php

namespace App\Service\Template;

use App\Entity\Raid;
use App\Entity\RaidTemplate;
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

	public function generateFromRaid($templateName, Raid $raid, $dayOfWeek)
	{
		$template = new RaidTemplate();
		$template
			->setName($templateName)
			->setRaidType($raid->getRaidType())
			->setExpectedAttendee($raid->getExpectedAttendee())
			->setDayOfWeek($dayOfWeek)
			->setInformation($raid->getInformation())
			->setMinTank($raid->getMinTank())
			->setMaxTank($raid->getMaxTank())
			->setMinHeal($raid->getMinHeal())
			->setMaxHeal($raid->getMaxHeal())
			->setUser($raid->getUser())
			->setStartAt($raid->getStartAt())
			->setEndAt($raid->getEndAt());

			$this->em->persist($template);
			$this->em->flush();

			return $template;
	}
}
