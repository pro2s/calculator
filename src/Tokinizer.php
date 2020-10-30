<?php

namespace Parser;

use Parser\Exceptions\ParseException;
use Parser\Exceptions\SyntaxException;
use Parser\Exceptions\RuntimeException;

class Tokinizer
{
    public const NUMBERS = '0123456789.';

    /**
     * string[]
     */
    private $tokens = [];

    public function __construct(array $tokens)
    {
        $this->tokens = $tokens;
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
     * @param string[] $acc
     * @param string $char
     *
     * @return string[]
     */
    public function addNumbers(array $acc, string $char): array
    {
        $last = end($acc);
        if ($last === false || $this->isToken($last)) {
            $numbers = '';
        } else {
            $numbers = array_pop($acc);
        }

        $acc[] = $numbers . $char;
        return $acc;
    }

    /**
     * @param string[] $tokens
     * @param string $token
     *
     * @return string[]
     */
    private function parseToken(array $tokens, string $token): array
    {
        if ($this->isNumber($token)) {
            return $this->addNumbers($tokens, $token);
        }

        if ($this->isToken($token)) {
            $tokens[] = $token;
            return $tokens;
        }

        throw new SyntaxException("Invalid character $token");
    }

    /**
     * @return string[]
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
