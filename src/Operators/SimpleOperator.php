<?php

namespace Parser\Operators;

abstract class SimpleOperator extends AbstractOperator
{
    public function getAssoc(): int
    {
        return self::LEFT;
    }

    public function getPercendence(): int
    {
        return self::SIMPLE_PERCENDENCE;
    }
}
