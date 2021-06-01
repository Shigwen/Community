<?php
namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class GreaterThanMinTank extends Constraint
{

	public function validatedBy()
	{
		return 'validator.greater_than_min_tank';
	}
}
