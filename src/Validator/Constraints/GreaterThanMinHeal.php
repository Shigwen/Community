<?php
namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class GreaterThanMinHeal extends Constraint
{

	public function validatedBy()
	{
		return 'validator.greater_than_min_heal';
	}
}
