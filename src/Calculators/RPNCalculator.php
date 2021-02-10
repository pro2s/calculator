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
        /** @var \SplStack<OperandInterface> $operands */
        $operands = new \SplStack();

        foreach ($tokens as $token) {
            if ($token instanceof OperandInterface) {
                $operands->push($token);
            } else {
                $count = $token->getArgumentsCount();
                $arguments = $this->getArguments($operands, $count);
                $value = $token->apply(...$arguments);
                $operands->push($this->operandFactory->create($value));
            }
        }

        if ($operands->isEmpty()) {
            throw new RuntimeException('Unexpected result');
        }

        $operand = $operands->pop();

        return $operand->getValue();
    }

    private function getArguments(\SplStack $operands, int $count): \SplStack
    {
        if ($count > $operands->count()) {
            throw new RuntimeException('Wrong arguments');
        }

        $arguments = new \SplStack();
        while ($count-- > 0) {
            $arguments->push($operands->pop());
        }

        return $arguments;
    }
}
