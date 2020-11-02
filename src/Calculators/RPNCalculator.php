<?php

namespace Parser\Calculators;

use Parser\Operands\OperandInterface;
use Parser\Exceptions\RuntimeException;
use Parser\Operators\OperatorInterface;
use Parser\Calculators\CalculatorInterface;
use Parser\Operands\OperandFactoryInterface;

class RPNCalculator implements CalculatorInterface
{
    private $operandFactory;

    public function __construct(OperandFactoryInterface $operandFactory)
    {
        $this->operandFactory = $operandFactory;
    }

    /**
     * @param \Iterator<OperatorInterface|OperandInterface> $tokens
     */
    public function calculate(\Iterator $tokens)
    {
        $operands = [];

        foreach ($tokens as $token) {
            if ($token instanceof OperandInterface) {
                $operands[] = $token;
            } else {
                $second = array_pop($operands);
                $first = array_pop($operands);

                if ($first === null || $second === null) {
                    throw new RuntimeException('Wrong arguments');
                }

                $operands[] = $this->operandFactory->create($token->apply($first, $second));
            }
        }

        $operand = end($operands);
        if ($operand instanceof OperandInterface) {
            return $operand->getValue();
        }

        throw new RuntimeException('Unexcepted result');
    }
}
