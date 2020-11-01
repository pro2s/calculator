<?php

namespace Parser;

use Parser\Operators\Add;
use Parser\Operators\Div;
use Parser\Operators\Mod;
use Parser\Operators\Pow;
use Parser\Operators\Sub;
use Parser\Operators\Mult;
use Parser\Operators\OpenBracket;
use Parser\Operators\CloseBracket;
use Parser\Operands\DecimalFactory;
use Parser\Calculators\RPNCalculator;
use Parser\Exceptions\ParseException;
use Parser\Operands\OperandInterface;
use Parser\Exceptions\SyntaxException;
use Parser\Exceptions\RuntimeException;
use Parser\Operators\OperatorInterface;
use Parser\Calculators\CalculatorInterface;
use Parser\Operands\OperandFactoryInterface;

class ShuntingYard implements ParserInterface
{
    /**
     * @var Tokinizer
     */
    private $tokenizer;

    /**
     * @var CalculatorInterface
     */
    private $calculator;

    public function __construct()
    {
        $this->calculator = new RPNCalculator(new DecimalFactory());
        $this->tokenizer = new Tokinizer(
            new DecimalFactory(),
            new Add(),
            new Sub(),
            new Mult(),
            new Div(),
            new Mod(),
            new Pow(),
            new OpenBracket(),
            new CloseBracket()
        );
    }

    /**
     * @return numeric
     */
    public function parse(string $string)
    {
        $tokens = $this->tokenizer->tokenize($string);

        $rpnTokens = $this->getRPN($tokens);

        return $this->calculator->calculate($rpnTokens);
    }

    /**
     * Implemeys of https://en.wikipedia.org/wiki/Shunting-yard_algorithm
     * @param list<OperatorInterface|OperandInterface> $tokens
     *
     * @return list<OperatorInterface|OperandInterface>
     */
    public function getRPN(array $tokens): array
    {
        /** @var list<OperatorInterface> $stack */
        $stack = [];
        /** @var list<OperatorInterface|OperandInterface> $rpn */
        $rpn = [];

        foreach($tokens as $token) {
            // if the token is a number, then push it to the output queue.
            if ($token instanceof OperandInterface) {
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
            } else {
                // while there is an operator at the top of the operator stack with
                // greater than or equal to precedence:
                while ($stack && $token->lessOrEqual(end($stack))) {
                    // pop operators from the operator stack, onto the output queue.
                    $rpn[] = array_pop($stack);
                }
                // push the read operator onto the operator stack.
                $stack[] = $token;
            }
        }

        // After while loop, if operator stack not null, pop everything to output queue
        while ($token = array_pop($stack)) {
            // If the operator token on the top of the stack is a parenthesis,
            // then there are mismatched parentheses.
            if ($token instanceof OpenBracket) {
                throw new ParseException("Mismatched parentheses!");
            }

            $rpn[] = $token;
        }

        return $rpn;
    }
}
