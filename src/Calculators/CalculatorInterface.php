<?php

namespace Parser\Calculators;

use Parser\Operands\OperandInterface;
use Parser\Operators\OperatorInterface;

interface CalculatorInterface
{
    /**
     * @param (OperatorInterface|OperandInterface)[] $tokens
     *
     * @return numeric
     */
    public function calculate(array $tokens);
}
