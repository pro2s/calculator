<?php

namespace Parser\Operators;

use Parser\Operands\OperandInterface;

class Mult extends BasicOperator
{
    protected const TOKEN = '*';

    public function apply(OperandInterface ...$operands)
    {
        [$operandA, $operandB] = $operands;

        return $operandA->getValue() * $operandB->getValue();
    }
}
