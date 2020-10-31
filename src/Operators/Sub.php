<?php

namespace Parser\Operators;

use Parser\Operands\OperandInterface;

class Sub extends BasicOperator
{
    protected const TOKEN = '-';

    public function apply(OperandInterface $operandA, OperandInterface $operandB)
    {
        return $operandA->getValue() - $operandB->getValue();
    }
}
