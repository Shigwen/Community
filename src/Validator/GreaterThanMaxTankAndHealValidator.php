<?php

namespace App\Validator;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class GreaterThanMaxTankAndHealValidator extends ConstraintValidator
{
    protected $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function validate($expectedAttendee, Constraint $constraint)
    {
        if (!$expectedAttendee) {
            return;
        }

        $raidForm = $this->context->getObject();

        if ($expectedAttendee < $raidForm->getMaxTank() + $raidForm->getMaxHeal()) {
            $this->context
                ->buildViolation("The maximum number of tanks and healers combined cannot be superior to the amount of raiders you're looking for")
                ->addViolation();
        }
    }
}
