<?php

namespace Parser\Operators;

use Parser\Operands\OperandInterface;

abstract class FunctionOperator extends AbstractOperator
{
    public function getAssoc(): int
    {
        return self::LEFT;
    }

    public function getPercendence(): int
    {
        return self::EMPTY_PERCENDENCE;
    }
}
