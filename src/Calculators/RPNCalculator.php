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

    public function calculate(\Iterator $tokens)
    {
        $operands = [];

        foreach ($tokens as $token) {
            if ($token instanceof OperandInterface) {
                $operands[] = $token;
            } else {
                $count = $token->getArgumentsCount();
                if ($count > count($operands)) {
                    throw new RuntimeException('Wrong arguments');
                }
                /** @var list<OperandInterface> $arguments */
                $arguments = \array_splice($operands, -$count);

                $operands[] = $this->operandFactory->create($token->apply(...$arguments));
            }
        }

        $operand = end($operands);
        if ($operand instanceof OperandInterface) {
            return $operand->getValue();
        }

        throw new RuntimeException('Unexpected result');
    }
}
