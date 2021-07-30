<?php

namespace Parser\Syntax;

use Parser\Operands\OperandInterface;
use Parser\Exceptions\RuntimeException;
use Parser\Operators\AbstractOperator;

class EmptyOperator extends AbstractOperator
{
    public function getAssoc(): int
    {
        return self::LEFT;
    }

    public function getPrecedence(): int
    {
        return self::EMPTY_PRECEDENCE;
    }

    /**
     * @throws RuntimeException
     */
    public function apply(OperandInterface ...$operands)
    {
        throw new RuntimeException('Operator can not be applied');
    }
}
