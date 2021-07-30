<?php

namespace Parser\Operators;

use Parser\Operands\OperandInterface;
use Parser\Exceptions\RuntimeException;

class Mod extends BasicOperator
{
    protected const TOKEN = '%';

    /**
     * @throws RuntimeException
     */
    public function apply(OperandInterface ...$operands): int
    {
        [$operandA, $operandB] = $operands;

        if (empty($operandB->getValue())) {
            throw new RuntimeException();
        }

        return $operandA->getValue() % $operandB->getValue();
    }
}
