<?php

namespace Parser\Operands;

use Parser\Exceptions\ParseException;
use Parser\Exceptions\SyntaxException;

class DecimalOperand implements OperandInterface
{
    public const NUMBERS = '0123456789.';

    /**
     * @var numeric|string
     */
    private $numbers = '';

    /**
     * @var numeric|null
     */
    private $value;

    /**
     * @param numeric|null $value
     */
    public function __construct($value = null)
    {
        $this->value = $value;
    }

    /**
     * @return numeric
     */
    public function getValue()
    {
        if ($this->value !== null) {
            return $this->value;
        }

        if (!is_numeric($this->numbers)) {
            throw new ParseException("Invalid number {$this->numbers}");
        }

        return $this->numbers;
    }

    public function parseToken(string $token): OperandInterface
    {
        if (!$this->isNumber($token)) {
            throw new SyntaxException("Invalid character $token");
        }
        $this->numbers .= $token;

        return $this;
    }

    private function isNumber(string $char): bool
    {
        return strspn($char, self::NUMBERS) !== 0;
    }
}
