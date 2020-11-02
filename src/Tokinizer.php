<?php

namespace Parser;

use Parser\Operands\OperandInterface;
use Parser\Operators\OperatorInterface;
use Parser\Operands\OperandFactoryInterface;

class Tokinizer
{
    /**
     * @var OperatorInterface[]
     */
    private $operators = [];

    /**
     * @var OperandFactoryInterface
     */
    private $operandFactory;

    public function __construct(
        OperandFactoryInterface $operandFactory,
        OperatorInterface ...$operators
    ) {
        $this->operandFactory = $operandFactory;
        foreach ($operators as $operator) {
            $this->operators[$operator->getToken()] = $operator;
        }
    }

    private function isOperator(string $char): bool
    {
        return isset($this->operators[$char]);
    }

    private function getOperand(string $token): OperandInterface
    {
        return $this->operandFactory->create($token);
    }

    private function getOperator(string $token): OperatorInterface
    {
        return $this->operators[$token];
    }

    public function getOperatorsPattern(): string
    {
        $symbols = \preg_quote(implode('', \array_keys($this->operators)), '/');

        return "/([$symbols])/";
    }

    /**
     * @return string[]
     */
    private function parseString(string $string): array
    {
        $tokens = \preg_split($this->getOperatorsPattern(), $string, -1, PREG_SPLIT_DELIM_CAPTURE);

        return \array_map(fn (string $token): string => trim($token), $tokens);
    }

    /**
     * @return \Generator<OperatorInterface|OperandInterface>
     */
    public function tokenize(string $string): \Generator
    {
        $tokens = $this->parseString($string);

        foreach ($tokens as $token) {
            if (empty($token)) {
                continue;
            }

            if ($this->isOperator($token)) {
                yield $this->getOperator($token);
                continue;
            }

            yield $this->getOperand($token);
        };
    }
}
