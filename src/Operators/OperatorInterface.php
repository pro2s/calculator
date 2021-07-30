<?php

namespace Parser\Operators;

use Parser\Operands\OperandInterface;

interface OperatorInterface
{
    /**
     * @return numeric
     */
    public function apply(OperandInterface ...$operands);

    public function getToken(): string;

    public function lessOrEqual(OperatorInterface $operator): bool;

    public function getAssoc(): int;

    public function getPrecedence(): int;

    public function getArgumentsCount(): int;
}
