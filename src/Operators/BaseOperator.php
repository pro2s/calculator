<?php

namespace Parser\Operators;

abstract class BaseOperator extends AbstractOperator
{
    public function getAssoc(): int
    {
        return self::LEFT;
    }

    public function getPercendence(): int
    {
        return self::BASE_PERCENDENCE;
    }
}
