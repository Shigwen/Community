<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class GreaterThanStartAt extends Constraint
{
    public function validatedBy()
    {
        return static::class . 'Validator';
    }
}
