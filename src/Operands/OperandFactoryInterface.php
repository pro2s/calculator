<?php

namespace Parser\Operands;

interface OperandFactoryInterface
{
    /**
     * @param mixed|null $value
     */
    public function create($value = null): OperandInterface;
}
