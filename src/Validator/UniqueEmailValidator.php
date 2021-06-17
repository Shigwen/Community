<?php

namespace App\Validator;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueEmailValidator extends ConstraintValidator
{
    protected $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function validate($email, Constraint $constraint)
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);

        if ($user) {
            $this->context
                ->buildViolation('Email already used.')
                ->addViolation();
        }
    }
}
