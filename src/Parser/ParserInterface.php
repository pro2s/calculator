<?php

namespace Parser\Parser;

use Parser\Operands\OperandInterface;
use Parser\Operators\OperatorInterface;
use Generator;

interface ParserInterface
{
    /**
     * @param string $string
     * @return Generator<OperatorInterface|OperandInterface>
     */
    public function parse(string $string): Generator;
}
