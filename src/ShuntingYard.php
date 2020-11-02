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
     * Implements of https://en.wikipedia.org/wiki/Shunting-yard_algorithm
     * @param \Iterator<OperatorInterface|OperandInterface> $tokens
     *
     * @return \Generator<OperatorInterface|OperandInterface>
     */
    public function getRPN(\Iterator $tokens): \Generator
    {
        /** @var \SplStack<OperatorInterface> $stack */
        $stack = new \SplStack();

        foreach ($tokens as $token) {
            // if the token is a number, then push it to the output queue.
            if ($token instanceof OperandInterface) {
                yield $token;

            // if the token is a left bracket (i.e. "("), then:
            } elseif ($token instanceof OpenBracket) {
                // push it onto the operator stack.
                $stack->push($token);
            // if the token is a right bracket (i.e. ")"), then:
            } elseif ($token instanceof CloseBracket) {
                // while the operator at the top of the operator stack is not a left bracket:
                while (!($stack->top() instanceof OpenBracket)) {
                    // pop operators from the operator stack onto the output queue.
                    yield $stack->pop();
                    // if the stack runs out without finding a left bracket, then there are
                    // mismatched parentheses. */
                    if ($stack->isEmpty()) {
                        throw new ParseException("Mismatched parentheses!");
                    }
                }
                // pop the left bracket from the stack.
                $stack->pop();
            // if the token is an operator, then:
            } else {
                // while there is an operator at the top of the operator stack with
                // greater than or equal to precedence:
                while (!$stack->isEmpty() && $token->lessOrEqual($stack->top())) {
                    // pop operators from the operator stack, onto the output queue.
                    yield $stack->pop();
                }
                // push the read operator onto the operator stack.
                $stack->push($token);
            }
        }

        // after while loop, if operator stack not null
        while (!$stack->isEmpty()) {
            $token = $stack->pop();
            // if the operator token on the top of the stack is a parenthesis,
            // then there are mismatched parentheses.
            if ($token instanceof OpenBracket) {
                throw new ParseException("Mismatched parentheses!");
            }
            // pop everything to output queue
            yield $token;
        }
    }
}
