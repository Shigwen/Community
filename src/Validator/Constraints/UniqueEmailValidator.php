<?php
namespace App\Validator\Constraints;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueEmailValidator extends ConstraintValidator
{
   protected $em;

   public function __construct(EntityManager $entityManager)
   {
      $this->em = $entityManager;
   }

  public function validate($value, Constraint $constraint)
  {
      $user = $this->em->getRepository(User::class)->findOneBy(['email' => $value]);

      if ($user) {
          $this->context->buildViolation($constraint->message)
              ->addViolation();
      }
  }
}
