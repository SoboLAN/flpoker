<?php

namespace FileListPoker\Main\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class PositiveNumberValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof PositiveNumber) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\PositiveNumber');
        }
        
        $valid = true;
        
        if (is_null($value)) {
            $valid = false;
        }
        
        $valueAsString = (string) $value;
        
        if (strlen($valueAsString) === 0 || strlen($valueAsString) > $constraint->maxLength) {
            $valid = false;
        }
        
        if (! ctype_digit($valueAsString)) {
            $valid = false;
        }
        
        if (! $valid) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{value}}', $value)
                ->addViolation();
        }
    }
}
