<?php

namespace Parser\Operands;

use Parser\Exceptions\SyntaxException;

class DecimalFactory implements OperandFactoryInterface
{
    /**
     * @param numeric|null $value
     * @throws SyntaxException
     */
    public function create($value = null): OperandInterface
    {
        return new DecimalOperand($value);
    }
}
