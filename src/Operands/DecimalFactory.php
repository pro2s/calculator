<?php

namespace Parser\Operands;

class DecimalFactory implements OperandFactoryInterface
{
    /**
     * @param numeric|null $value
     */
    public function create($value = null): OperandInterface
    {
        return new DecimalOperand($value);
    }
}
