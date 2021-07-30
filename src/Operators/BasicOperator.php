<?php

namespace Parser\Operators;

abstract class BasicOperator extends AbstractOperator
{
    public function getAssoc(): int
    {
        return self::LEFT;
    }

    public function getPrecedence(): int
    {
        return self::BASIC_PRECEDENCE;
    }
}
