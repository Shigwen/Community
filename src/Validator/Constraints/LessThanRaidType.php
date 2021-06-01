<?php
namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class LessThanRaidType extends Constraint
{
	public $message = 'The number of people you are looking for must be inferior to the size of the raid.';

	public function validatedBy()
	{
		return 'validator.less_than_raid_type';
	}
}
