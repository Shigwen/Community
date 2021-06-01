<?php
namespace App\Validator\Constraints;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class LessThanRaidTypeValidator extends ConstraintValidator
{
   protected $em;

   public function __construct(EntityManager $entityManager)
   {
      $this->em = $entityManager;
   }

  public function validate($value, Constraint $constraint)
  {
	  $raidForm = $this->context->getObject()->getParent()->getViewData();

	  if ($value >= $raidForm->getRaidType()) {
		$this->context
			->buildViolation($constraint->message)
			->addViolation();
	  }
  }
}
