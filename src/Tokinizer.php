<?php

namespace Parser;

use Parser\Operators\AbstractOperator;
use Parser\Operands\DecimalOperand;
use Parser\Operands\OperandInterface;
use Parser\Exceptions\ParseException;
use Parser\Exceptions\SyntaxException;
use Parser\Exceptions\RuntimeException;

class Tokinizer
{
    /**
     * @var AbstractOperator[]
     */
    private $tokens = [];

    public function __construct(AbstractOperator ...$operators)
    {
        foreach ($operators as $operator) {
            $this->tokens[$operator->getToken()] = $operator;
        }
    }

    private function isToken(string $char): bool
    {
        return isset($this->tokens[$char]);
    }

    /**
     * @return string[]
     */
    private function splitString(string $string): array
    {
        $chars = str_split($string);

        return array_filter($chars, fn (string $char): bool => strlen(trim($char)) !== 0);
    }

    /**
     * @param string[] $tokens
     * @param string $token
     *
     * @return (AbstractOperator|OperandInterface)[]
     */
    private function parseToken(array $tokens, string $token): array
    {
        if ($this->isToken($token)) {
            $tokens[] = $this->tokens[$token];

            return $tokens;
        }

        if (end($tokens) instanceof OperandInterface) {
            $operand = array_pop($tokens);
        } else {
            // TODO: Replace with fabric
            $operand = new DecimalOperand();
        }

        $operand->parseToken($token);

        $tokens[] = $operand;

        return $tokens;
    }

    /**
     * @return (AbstractOperator|OperandInterface)[]
     */
    public function tokenize(string $string): array
    {
        $chars = $this->splitString($string);

        return array_reduce(
            $chars,
            fn (array $acc, string $char): array => $this->parseToken($acc, $char),
            []
        );
    }
}
