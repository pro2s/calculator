<?php

namespace Parser;

use Parser\Operators\Add;
use Parser\Operators\Sub;
use Parser\Operators\Mult;
use Parser\Operators\OpenBracket;
use Parser\Operators\CloseBracket;
use Parser\Exceptions\ParseException;
use Parser\Exceptions\SyntaxException;
use Parser\Operators\AbstractOperator;
use Parser\Exceptions\RuntimeException;

class ShuntingYard implements ParserInterface
{
    public const DIV = '/';
    public const POW = '^';
    public const MOD = '%';

    public const LEFT = 0;
    public const RIGHT = 1;

    public const PRECEDENCE = [
        self::DIV => 3,
        self::MOD => 3,
        self::POW => 4,
    ];

    public const ASSOC = [
        self::DIV => self::LEFT,
        self::MOD => self::LEFT,
        self::POW => self::RIGHT,
    ];

    /**
     * @var (null|numeric|string)[]
     */
    private $queue = [];

    /**
     * @var Tokinizer
     */
    private $tokenizer;

    public function __construct()
    {
        $this->tokenizer = new Tokinizer(
            new Add(),
            new Sub(),
            new Mult(),
            new OpenBracket(),
            new CloseBracket()
        );
    }

    /**
     * @return false|numeric
     */
    public function parse(string $string)
    {
        $chars = $this->splitString($string);

        $tokens = $this->tokenizer->tokenize($chars);

        $rpn = $this->getRPN($tokens);

        return $this->calculate($rpn);
    }

    private function isOperator(string $char): bool
    {
        return isset($this->operators[$char]);
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
     * @param (AbstractOperator|string)[] $tokens
     */
    public function getRPN(array $tokens): array
    {
        /** @var AbstractOperator[] $stack */
        $stack = [];
        /** @var (numeric|AbstractOperator)[] $rpn */
        $rpn = [];

        while ($token = array_shift($tokens)) {
            // if the token is a number, then push it to the output queue.
            if (is_numeric($token)) {
                $rpn[] = $token;

            // if the token is a left bracket (i.e. "("), then:
            } elseif ($token instanceof OpenBracket) {
                // push it onto the operator stack.
                $stack[] = $token;

            // if the token is a right bracket (i.e. ")"), then:
            } elseif ($token instanceof CloseBracket) {
                // while the operator at the top of the operator stack is not a left bracket:
                while (!(end($stack) instanceof OpenBracket)) {
                    // pop operators from the operator stack onto the output queue.
                    $rpn[] = array_pop($stack);

                    // if the stack runs out without finding a left bracket, then there are
                    // mismatched parentheses. */
                    if (!$stack) {
                        throw new ParseException("Mismatched parentheses!");
                    }
                }
                // pop the left bracket from the stack.
                array_pop($stack);
                // if the token is an operator, then:
            } elseif ($token instanceof AbstractOperator) {
                // while there is an operator at the top of the operator stack with
                // greater than or equal to precedence:
                while ($stack && $token->lessOrEqual(end($stack))) {
                    // pop operators from the operator stack, onto the output queue.
                    $rpn[] = array_pop($stack);
                }
                // push the read operator onto the operator stack.
                $stack[] = $token;
            } else {
                throw new ParseException("Unexpected token $token");
            }
        }

        while ($token = array_pop($stack)) {
            if ($token instanceof OpenBracket) {
                throw new ParseException("Mismatched parentheses!");
            }

            $rpn[] = $token;
        }

        return $rpn;
    }

    /**
     * @return false|numeric
     */
    public function calculate(array $rpn)
    {
        /** @var numeric[] */
        $stack = [];
        while ($token = array_shift($rpn)) {
            if (is_numeric($token)) {
                $stack[] = $token;
            } else if ($token instanceof AbstractOperator) {
                $secondOperand = array_pop($stack);
                $firstOperand = array_pop($stack);
                $stack[] = $token->apply($firstOperand, $secondOperand);
            } else {
                throw new RuntimeException('Uncknown argument');
            }
        }

        return end($stack);
    }
}
