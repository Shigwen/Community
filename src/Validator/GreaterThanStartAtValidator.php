<?php

namespace App\Validator;

use DateTimeZone;
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

    public function validate($formEndAt, Constraint $constraint)
    {
        if (!$formEndAt) {
            return;
        }

        $raidForm = $this->context->getObject();
        $timezone = new DateTimeZone($raidForm->getUser()->getTimezone()->getName());

        $startAt = clone $raidForm->getStartAt();
        $endAt = clone $formEndAt;

        $startAt->setTimezone($timezone);
        $endAt->setTimezone($timezone);

        $interval = $startAt->diff($endAt);
        if ($startAt > $endAt || $interval->h === 0) {
            $this->context
                ->buildViolation("The end of the raid must be minimum 1 hour after the start")
                ->addViolation();
        }
    }
}
