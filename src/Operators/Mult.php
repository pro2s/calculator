<?php

namespace Parser\Operators;

class Mult extends BasicOperator
{
    protected const TOKEN = '*';

    public function apply($operandA, $operandB)
    {
        return $operandA * $operandB;
    }
}
