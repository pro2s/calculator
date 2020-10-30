<?php

namespace Parser\Operators;

class Add extends BasicOperator
{
    protected const TOKEN = '+';

    public function apply($operandA, $operandB)
    {
        return $operandA + $operandB;
    }
}
