<?php

namespace Parser;

use Generator;
use Iterator;
use Parser\Calculators\CalculatorInterface;
use Parser\Calculators\RPNCalculator;
use Parser\Exceptions\ParseException;
use Parser\Functions\Min;
use Parser\Functions\Sqrt;
use Parser\Functions\FunctionOperator;
use Parser\Operators\Add;
use Parser\Operators\Div;
use Parser\Operators\Mod;
use Parser\Operators\Pow;
use Parser\Operators\Sub;
use Parser\Operators\Mult;
use Parser\Operators\OperatorInterface;
use Parser\Operands\DecimalFactory;
use Parser\Operands\OperandInterface;
use Parser\Syntax\Comma;
use Parser\Syntax\OpenBracket;
use Parser\Syntax\CloseBracket;

class ShuntingYard implements ParserInterface
{
    private Tokinizer $tokenizer;

    private CalculatorInterface $calculator;

    public function __construct()
    {
        $operand = new DecimalFactory();
        $this->calculator = new RPNCalculator($operand);
        $this->tokenizer = new Tokinizer(
            $operand,
            new Min(),
            new Sqrt(),
            new Add(),
            new Sub(),
            new Mult(),
            new Div(),
            new Mod(),
            new Pow(),
            new Comma(),
            new OpenBracket(),
            new CloseBracket()
        );
    }

    /**
     * @return numeric
     * @throws ParseException
     * @throws Exceptions\RuntimeException
     */
    public function parse(string $string)
    {
        $tokens = $this->tokenizer->tokenize($string);

        $rpnTokens = $this->getRPN($tokens);

        return $this->calculator->calculate($rpnTokens);
    }

    /**
     * Implements of https://en.wikipedia.org/wiki/Shunting-yard_algorithm
     * @param Iterator<OperatorInterface|OperandInterface> $tokens
     *
     * @return Generator<OperatorInterface|OperandInterface>
     * @throws ParseException
     */
    public function getRPN(Iterator $tokens): Generator
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
            // if the token is a function token, then push it onto the stack.
            } elseif ($token instanceof FunctionOperator) {
                $stack->push($token);
            // If the token is a function argument separator (e.g., a comma):
            } elseif ($token instanceof Comma) {
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
