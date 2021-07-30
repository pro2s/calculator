<?php

namespace Parser\Calculators;

use Parser\Operators\OperatorInterface;
use Parser\Operands\OperandInterface;

interface CalculatorInterface
{
    /**
     * @param \Iterator<OperatorInterface|OperandInterface> $tokens
     *  @return numeric
     */
    public function calculate(\Iterator $tokens);
}
