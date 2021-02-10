<?php

namespace Parser\Functions;

use Parser\Operands\OperandInterface;
use Parser\Operators\AbstractOperator;

abstract class FunctionOperator extends AbstractOperator
{
    public function getAssoc(): int
    {
        return self::LEFT;
    }

    public function getPrecedence(): int
    {
        return self::EMPTY_PERCENDENCE;
    }
}
