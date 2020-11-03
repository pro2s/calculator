<?php

namespace Parser\Operators;

use Parser\Operands\OperandInterface;
use Parser\Exceptions\RuntimeException;

class Mod extends BasicOperator
{
    protected const TOKEN = '%';

    public function apply(OperandInterface ...$operands)
    {
        [$operandA, $operandB] = $operands;

        if (empty($operandB->getValue())) {
            throw new RuntimeException();
        }

        return $operandA->getValue() % $operandB->getValue();
    }
}
