<?php

namespace Parser\Functions;

use Parser\Operands\OperandInterface;

class Min extends FunctionOperator
{
    protected const TOKEN = 'min';

    public function apply(OperandInterface ...$operands)
    {
        [$operandA, $operandB] = $operands;

        return min(0 + $operandA->getValue(), 0 + $operandB->getValue());
    }
}
