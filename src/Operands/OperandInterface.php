<?php

namespace Parser\Operands;

use Parser\TokenInterface;

interface OperandInterface extends TokenInterface
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
