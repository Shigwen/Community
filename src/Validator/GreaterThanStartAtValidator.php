<?php

namespace App\Validator;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class GreaterThanStartAtValidator extends ConstraintValidator
{
    protected $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function validate($endAt, Constraint $constraint)
    {
        if (!$endAt) {
            return;
        }

        $raidForm = $this->context->getObject();
        $startAt = $raidForm->getStartAt();

        $endAt->setDate(
            $startAt->format('Y'),
            $startAt->format('m'),
            $startAt->format('d')
        );

        $interval = $startAt->diff($endAt);
        if ($startAt > $endAt || $interval->h === 0) {
            $this->context
                ->buildViolation("The end of the raid must be minimum 1 hour after the start")
                ->addViolation();
        }
    }
}
