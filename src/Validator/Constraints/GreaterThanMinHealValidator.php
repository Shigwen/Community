<?php
namespace App\Validator\Constraints;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class GreaterThanMinHealValidator extends ConstraintValidator
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

		if ($value < $raidForm->getMinHeal()) {
			$this->context
				->buildViolation("The minimum amount of healers you are looking for cannot be superior to the maximum amount of heals you're looking for.")
				->addViolation();
		}
	}
}
