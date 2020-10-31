<?php

namespace Parser\Operands;

use Parser\Operands\OperandInterface;

interface OperandFactoryInterface
{
    /**
     * @param mixed|null $value
     */
    public function create($value = null): OperandInterface;
}
