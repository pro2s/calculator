<?php

namespace Parser\Operands;

class DecimalFactory implements OperandFactoryInterface
{
    public function create($value = null): OperandInterface
    {
        return new DecimalOperand($value);
    }
}
