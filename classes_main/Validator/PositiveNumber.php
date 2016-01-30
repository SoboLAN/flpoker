<?php

namespace FileListPoker\Main\Validator;

use Symfony\Component\Validator\Constraint;

class PositiveNumber extends Constraint
{
    public $message = 'This value ( {{value}} ) should be or represent a positive integer';
    
    public $maxLength = PHP_INT_SIZE;
}
