<?php

namespace Parser\Operators;

use Parser\Operands\OperandInterface;

class Pow extends AbstractOperator
{
    public function getAssoc(): int
    {
        return self::RIGHT;
    }

    public function getPercendence(): int
    {
        return self::COMPLEX_PERCENDENCE;
    }

    protected const TOKEN = '^';

    public function apply(OperandInterface ...$operands)
    {
        [$operandA, $operandB] = $operands;

        return $operandA->getValue() ** $operandB->getValue();
    }
}
