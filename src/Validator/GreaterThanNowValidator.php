<?php

namespace App\Validator;

use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class GreaterThanNowValidator extends ConstraintValidator
{
    protected $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function validate($formStartAt, Constraint $constraint)
    {
        if (!$formStartAt) {
            return;
        }

        $raidForm = $this->context->getObject();
        $timezone = new DateTimeZone($raidForm->getUser()->getTimezone()->getName());

        $now = new DateTime('now', $timezone);
        $startAt = clone $formStartAt;
        $startAt->setTimezone($timezone);

        if ($now > $startAt) {
            $this->context
                ->buildViolation("The start must be in the future")
                ->addViolation();
        }
    }
}
