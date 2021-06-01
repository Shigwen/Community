<?php
namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class GreaterThanMaxTankAndHeal extends Constraint
{

	public function validatedBy()
	{
		return 'validator.greater_than_max_tank_and_heal';
	}
}
