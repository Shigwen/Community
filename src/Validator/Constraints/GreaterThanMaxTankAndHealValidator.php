<?php
namespace App\Validator\Constraints;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class GreaterThanMaxTankAndHealValidator extends ConstraintValidator
{
	protected $em;

	public function __construct(EntityManager $entityManager)
	{
		$this->em = $entityManager;
	}

	public function validate($value, Constraint $constraint)
	{
		if (!$value) {
			return;
		}

		$raidForm = $this->context->getObject()->getParent()->getViewData();

		if ($raidForm->getMaxTank() + $raidForm->getMaxHeal() > $raidForm->getExpectedAttendee()) {
			$this->context
				->buildViolation("The maximum number of tanks and healers combined cannot be superior to the amount of raiders you're looking for")
				->addViolation();
		}
	}
}
