<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class SanitizerValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var Sanitizer $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        $value = strtolower($value);
        foreach ($constraint->banWords as $banWord) {
            if (str_contains($value, $banWord)) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ banWord }}', $banWord)
                    ->addViolation();
            }
        }
    }
}
