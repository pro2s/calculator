<?php

namespace Parser\Operators;

use Parser\Operands\OperandInterface;
use Parser\Exceptions\RuntimeException;

class EmptyOperator extends AbstractOperator
{
    public function getAssoc(): int
    {
        return self::LEFT;
    }

    public function getPercendence(): int
    {
        return self::EMPTY_PERCENDENCE;
    }

    public function apply(OperandInterface ...$operands)
    {
        throw new RuntimeException('Operator can not be applied');
    }
}
