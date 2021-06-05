<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

class UniqueEmail extends Constraint
{
    public function validatedBy()
    {
        return static::class . 'Validator';
    }
}
