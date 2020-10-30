<?php

namespace Parser\Operators;

class Sub extends BasicOperator
{
    protected const TOKEN = '-';

    public function apply($operandA, $operandB)
    {
        return $operandA - $operandB;
    }
}
