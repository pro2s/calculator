<?php

namespace Parser;

use Parser\Exceptions\ParseException;
use Parser\Exceptions\SyntaxException;
use Parser\Exceptions\RuntimeException;

class ShuntingYard implements ParserInterface
{
    public const PLUS = '+';
    public const MINUS = '-';
    public const MULT = '*';
    public const DIV = '/';
    public const POW = '^';
    public const MOD = '%';
    public const OPEN_BRACKET = '(';
    public const CLOSE_BRACKET = ')';

    public const OPERATORS = [
        self::PLUS => self::PLUS,
        self::MINUS => self::MINUS,
        self::MULT => self::MULT,
    ];

    public const LEFT = 0;
    public const RIGHT = 0;

    public const PRECEDENCE = [
        self::PLUS => 2,
        self::MINUS => 2,
        self::MULT => 3,
        self::DIV => 3,
        self::MOD => 3,
        self::POW => 4,
        self::OPEN_BRACKET => 0,
        self::CLOSE_BRACKET => 0,
    ];

    public const ASSOC = [
        self::PLUS => self::LEFT,
        self::MINUS => self::LEFT,
        self::MULT => self::LEFT,
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
        $this->tokenizer = new Tokinizer(self::PRECEDENCE);
    }

    /**
     * @return false|float|int|numeric
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
        return isset(self::OPERATORS[$char]);
    }

    /**
     * @return string[]
     */
    private function splitString(string $string): array
    {
        return array_filter(str_split($string), fn (string $char): bool => strlen(trim($char)) !== 0);
    }

    private function checkToken(string $token, string $lastToken): bool
    {
        return self::PRECEDENCE[$lastToken] >= self::PRECEDENCE[$token] + self::ASSOC[$token];
    }

    /**
     * @param string[] $tokens
     */
    public function getRPN(array $tokens): array
    {
        /** @var string[] $stack */
        $stack = [];
        $rpn = [];

        while ($token = array_shift($tokens)) {
            // if the token is a number, then push it to the output queue.
            if (is_numeric($token)) {
                $rpn[] = $token;

            // if the token is an operator, then:
            } elseif ($this->isOperator($token)) {

                // while there is an operator at the top of the operator stack with
                // greater than or equal to precedence:
                while ($stack && $this->checkToken($token, end($stack))) {
                    // pop operators from the operator stack, onto the output queue.
                    $rpn[] = array_pop($stack);
                }
                // push the read operator onto the operator stack.
                $stack[] = $token;

            // if the token is a left bracket (i.e. "("), then:
            } elseif ($token === self::OPEN_BRACKET) {
                // push it onto the operator stack.
                $stack[] = $token;

            // if the token is a right bracket (i.e. ")"), then:
            } elseif ($token === self::CLOSE_BRACKET) {
                // while the operator at the top of the operator stack is not a left bracket:
                while (end($stack) !== self::OPEN_BRACKET) {
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
            } else {
                throw new ParseException("Unexpected token $token");
            }
        }

        while ($token = array_pop($stack)) {
            if ($token === self::OPEN_BRACKET) {
                throw new ParseException("Mismatched parentheses!");
            }

            $rpn[] = $token;
        }

        return $rpn;
    }

    /**
     * @return false|float|int|numeric
     */
    public function calculate(array $rpn)
    {
        $stack = [];
        while ($token = array_shift($rpn)) {
            if (is_numeric($token)) {
                $stack[] = $token;
            } else {
                $secondOperand = array_pop($stack);
                $firstOperand = array_pop($stack);

                switch ($token) {
                    case '*':
                        $stack[] = $firstOperand * $secondOperand;
                        break;
                    case '/':
                        $stack[] = $firstOperand / $secondOperand;
                        break;
                    case '-':
                        $stack[] = $firstOperand - $secondOperand;
                        break;
                    case '+':
                        $stack[] = $firstOperand + $secondOperand;
                        break;
                    case '^':
                        $stack[] = pow((float) $firstOperand, (float) $secondOperand);
                        break;
                    default:
                        throw new RuntimeException('Uncknown operator');
                }
            }
        }

        return end($stack);
    }
}
