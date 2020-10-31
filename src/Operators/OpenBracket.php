<?php

namespace Parser\Operators;

use Parser\Operands\OperandInterface;

class OpenBracket extends BaseOperator
{
    protected const TOKEN = '(';

    public function apply(OperandInterface $operandA, OperandInterface $operandB)
    {
    }
}
