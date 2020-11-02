<?php

namespace Parser\Operators;

use Parser\Operands\OperandInterface;

class Min extends FunctionOperator
{
    protected const TOKEN = 'min';

    public function apply(OperandInterface $operandA, OperandInterface $operandB)
    {
        return min(0 + $operandA->getValue(), 0 + $operandB->getValue());
    }
}
