<?php

namespace Parser\Operators;

abstract class AbstractOperator
{
    protected const TOKEN = '';

    public const LEFT = 0;
    public const RIGHT = 1;

    public const BASE_PERCENDENCE = 0;
    public const BASIC_PERCENDENCE = 2;
    public const SIMPLE_PERCENDENCE = 3;
    public const COMPLEX_PERCENDENCE = 4;

    abstract public function getAssoc(): int;

    abstract public function getPercendence(): int;

    /**
     * @param numeric $operandA
     * @param numeric $operandB
     *
     * @return numeric
     */
    abstract public function apply($operandA, $operandB);

    public function getToken(): string
    {
        return static::TOKEN;
    }

    public function lessOrEqual(AbstractOperator $operator): bool
    {
        return $operator->getPercendence() >= $this->getPercendence() + $this->getAssoc();
    }
}
