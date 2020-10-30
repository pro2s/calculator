<?php

namespace Parser;

use Parser\Exceptions\ParseException;
use Parser\Exceptions\SyntaxException;
use Parser\Operators\AbstractOperator;
use Parser\Exceptions\RuntimeException;

class Tokinizer
{
    public const NUMBERS = '0123456789.';

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

    private function isNumber(string $char): bool
    {
        return strspn($char, self::NUMBERS) !== 0;
    }

    private function isToken(string $char): bool
    {
        return isset($this->tokens[$char]);
    }

    /**
     * @param (AbstractOperator|string)[] $acc
     * @param string $char
     *
     * @return (AbstractOperator|string)[]
     */
    public function addNumbers(array $acc, string $char): array
    {
        $last = end($acc);
        if ($last === false || $last instanceof AbstractOperator) {
            $numbers = '';
        } else {
            $numbers = array_pop($acc);
        }

        /** @var string $numbers  */
        $acc[] = $numbers . $char;

        return $acc;
    }

    /**
     * @param string[] $tokens
     * @param string $token
     *
     * @return (AbstractOperator|string)[]
     */
    private function parseToken(array $tokens, string $token): array
    {
        if ($this->isNumber($token)) {
            return $this->addNumbers($tokens, $token);
        }

        if ($this->isToken($token)) {
            $tokens[] = $this->tokens[$token];

            return $tokens;
        }

        throw new SyntaxException("Invalid character $token");
    }

    /**
     * @return (AbstractOperator|string)[]
     */
    public function tokenize(array $string): array
    {
        return array_reduce(
            $string,
            fn (array $acc, string $char): array => $this->parseToken($acc, $char),
            []
        );
    }
}
