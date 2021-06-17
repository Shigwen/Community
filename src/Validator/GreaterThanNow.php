<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class GreaterThanNow extends Constraint
{
    public function validatedBy()
    {
        return static::class . 'Validator';
    }
}
