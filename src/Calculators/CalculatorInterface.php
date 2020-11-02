<?php

namespace Parser\Calculators;

interface CalculatorInterface
{
    /**
     *  @return numeric
     */
    public function calculate(\Iterator $tokens);
}
