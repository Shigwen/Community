<?php
namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class UniqueEmail extends Constraint
{
  public $message = 'The Email already used.';

  public function validatedBy()
  {
      return 'unique.email.validator';
  }
}
