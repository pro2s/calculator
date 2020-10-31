<?php

namespace Parser\Operators;

use Parser\Operands\OperandInterface;
use Parser\Operators\OperatorInterface;

abstract class AbstractOperator implements OperatorInterface
{
    protected const TOKEN = '';

    public const LEFT = 0;
    public const RIGHT = 1;

    public const EMPTY_PERCENDENCE = 0;
    public const BASIC_PERCENDENCE = 2;
    public const SIMPLE_PERCENDENCE = 3;
    public const COMPLEX_PERCENDENCE = 4;

    abstract public function getAssoc(): int;

    abstract public function getPercendence(): int;

    /**
     * @return numeric
     */
    abstract public function apply(OperandInterface $operandA, OperandInterface $operandB);

    public function getToken(): string
    {
        return (string) static::TOKEN;
    }

    public function lessOrEqual(OperatorInterface $operator): bool
    {
        return $operator->getPercendence() >= $this->getPercendence() + $this->getAssoc();
    }
}
