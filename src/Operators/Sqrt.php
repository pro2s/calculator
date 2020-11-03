<?php

namespace Parser\Operators;

use Parser\Operands\OperandInterface;

class Sqrt extends FunctionOperator
{
    public const ARGUMENTS_COUNT = 1;

    protected const TOKEN = 'sqrt';

    public function apply(OperandInterface ...$operands)
    {
        [$operand] = $operands;

        return sqrt((float) $operand->getValue());
    }
}
