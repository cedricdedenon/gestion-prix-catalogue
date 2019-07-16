<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class RulesExpression extends Constraint
{
    public $message = 'La règle "{{ string }}" n\'est pas correcte';
}