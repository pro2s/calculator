<?php

namespace Parser\Calculators;

interface CalculatorInterface
{
    /**
     *  @return numeric
     */
    public function calculate(array $tokens);
}
