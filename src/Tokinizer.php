<?php

namespace Parser;

use Parser\Exceptions\ParseException;
use Parser\Operands\OperandInterface;
use Parser\Exceptions\SyntaxException;
use Parser\Exceptions\RuntimeException;
use Parser\Operators\OperatorInterface;
use Parser\Operands\OperandFactoryInterface;

class Tokinizer
{
    /**
     * @var OperatorInterface[]
     */
    private $tokens = [];

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
     * @param list<OperatorInterface|OperandInterface> $tokens
     * @param string $token
     *
     * @return list<OperatorInterface|OperandInterface>
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
            $operand = $this->operandFactory->create();
        }
        /** @var OperandInterface $operand */
        $operand->parseToken($token);

        $tokens[] = $operand;

        return $tokens;
    }

    /**
     * @return list<OperatorInterface|OperandInterface>
     */
    public function tokenize(string $string): array
    {
        $chars = $this->splitString($string);
        $tokens = [];

        foreach ($chars as $char) {
            $tokens = $this->parseToken($tokens, $char);
        };

        return $tokens;
    }
}
