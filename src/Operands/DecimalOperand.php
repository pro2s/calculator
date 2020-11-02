<?php

namespace Parser\Operands;

use Parser\Exceptions\ParseException;
use Parser\Exceptions\SyntaxException;

class DecimalOperand implements OperandInterface
{
    /**
     * @var numeric
     */
    private $value;

    /**
     * @param mixed $value
     */
    public function __construct($value)
    {
        if (!is_numeric($value)) {
            throw new SyntaxException("Invalid numeric operand $value");
        }

        $this->value = $value;
    }

    /**
     * @return numeric
     */
    public function getValue()
    {
        return $this->value;
    }
}
