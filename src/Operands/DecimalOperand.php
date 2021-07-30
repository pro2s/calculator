<?php

namespace Parser\Operands;

use Parser\Exceptions\SyntaxException;

class DecimalOperand implements OperandInterface
{
    /**
     * @var numeric
     */
    private $value;

    /**
     * @param mixed $value
     * @throws SyntaxException
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

    public function isValue(): bool
    {
        return true;
    }
}
