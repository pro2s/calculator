<?php

namespace Parser\Operators;

use Parser\Operands\OperandInterface;

abstract class AbstractOperator implements OperatorInterface
{
    protected const TOKEN = '';

    public const ARGUMENTS_COUNT = 2;

    public const LEFT = 0;
    public const RIGHT = 1;

    public const EMPTY_PRECEDENCE = 0;
    public const BASIC_PRECEDENCE = 2;
    public const SIMPLE_PRECEDENCE = 3;
    public const COMPLEX_PRECEDENCE = 4;

    abstract public function getAssoc(): int;

    abstract public function getPrecedence(): int;

    /**
     * @return numeric
     */
    abstract public function apply(OperandInterface ...$operands);

    public function getToken(): string
    {
        return (string) static::TOKEN;
    }

    public function lessOrEqual(OperatorInterface $operator): bool
    {
        return $operator->getPrecedence() >= $this->getPrecedence() + $this->getAssoc();
    }

    public function getArgumentsCount(): int
    {
        return (int) static::ARGUMENTS_COUNT;
    }
}
