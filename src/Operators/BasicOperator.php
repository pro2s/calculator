<?php

namespace Parser\Operators;

abstract class BasicOperator extends AbstractOperator
{
    public function getAssoc(): int
    {
        return self::LEFT;
    }

    public function getPercendence(): int
    {
        return self::BASIC_PERCENDENCE;
    }
}
