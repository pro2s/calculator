<?php

namespace Parser\Operands;

interface OperandInterface
{
    /**
     * @return numeric
     */
    public function getValue();

    public function parseToken(string $token): OperandInterface;

    /**
     * @param numeric|null $value
     */
    public function __construct($value = null);
}
