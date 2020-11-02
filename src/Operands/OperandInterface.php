<?php

namespace Parser\Operands;

interface OperandInterface
{
    /**
     * @return numeric
     */
    public function getValue();

    /**
     * @param mixed $value
     */
    public function __construct($value);
}
