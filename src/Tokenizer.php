<?php

namespace Parser;

use Parser\Operands\OperandInterface;
use Parser\Operators\OperatorInterface;
use Parser\Operands\OperandFactoryInterface;

class Tokenizer
{
    /**
     * @var OperatorInterface[]
     */
    private $operators = [];

    /**
     * @var string
     */
    private $operatorsPattern = '';

    /**
     * @var OperandFactoryInterface
     */
    private $operandFactory;

    public function __construct(
        OperandFactoryInterface $operandFactory,
        OperatorInterface ...$operators
    ) {
        $this->operandFactory = $operandFactory;

        $tokens = [];
        foreach ($operators as $operator) {
            $token = $operator->getToken();
            $this->operators[$token] = $operator;
            $tokens[] = \preg_quote($token, '/');
        }

        $this->operatorsPattern = implode('|', $tokens);
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
        return "/($this->operatorsPattern)/";
    }

    /**
     * @return string[]
     */
    private function parseString(string $string): array
    {
        $tokens = \preg_split($this->getOperatorsPattern(), $string, -1, PREG_SPLIT_DELIM_CAPTURE);

        return \array_map(fn (string $token): string => trim($token), $tokens);
    }

    private function isEmpty(string $token): bool
    {
        return $token === '';
    }

    /**
     * @return \Generator<OperatorInterface|OperandInterface>
     */
    public function tokenize(string $string): \Generator
    {
        $tokens = $this->parseString($string);

        foreach ($tokens as $token) {
            if ($this->isEmpty($token)) {
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
