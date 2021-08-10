<?php

use Parser\Calculators\RPNCalculator;
use Parser\Operands\OperandFactoryInterface;
use Parser\Parser\ShuntingYard\Parser;

final class ShuntingYardTest extends ParserTest
{
    function setupParser(OperandFactoryInterface $operandFactory): void
    {
        $this->parser = new Parser($operandFactory);
        $this->calculator = new RPNCalculator($operandFactory);
    }
}