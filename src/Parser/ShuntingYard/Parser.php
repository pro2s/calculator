<?php

namespace Parser\Parser\ShuntingYard;

use Generator;
use Iterator;
use Parser\Exceptions\ParseException;
use Parser\Functions\Min;
use Parser\Functions\Sqrt;
use Parser\Functions\FunctionOperator;
use Parser\Operands\OperandFactoryInterface;
use Parser\Operators\Add;
use Parser\Operators\Div;
use Parser\Operators\Mod;
use Parser\Operators\Pow;
use Parser\Operators\Sub;
use Parser\Operators\Mult;
use Parser\Operators\OperatorInterface;
use Parser\Operands\OperandInterface;
use Parser\Parser\ParserInterface;
use Parser\Syntax\Comma;
use Parser\Syntax\OpenBracket;
use Parser\Syntax\CloseBracket;
use Parser\TokenInterface;
use Parser\Tokenizer;
use SplStack;

class Parser implements ParserInterface
{
    private Tokenizer $tokenizer;

    public function __construct(OperandFactoryInterface $operandFactory)
    {
        $this->tokenizer = new Tokenizer(
            $operandFactory,
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
     * @param string $string
     * @return Generator<OperatorInterface|OperandInterface>
     * @throws ParseException
     */
    public function parse(string $string): Generator
    {
        $tokens = $this->tokenizer->tokenize($string);

        return $this->getRPN($tokens);
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
        /** @var SplStack<OperatorInterface> $stack */
        $stack = new SplStack();

        foreach ($tokens as $token) {
            switch (true) {
                // if the token is an operand, then:
                case $token instanceof OperandInterface:
                    // push it to the output queue
                    yield $token;
                    break;
                // if the token is a function token, then:
                case $token instanceof FunctionOperator:
                // if the token is a left bracket (i.e. "("), then:
                case $token instanceof OpenBracket:
                    // push it onto the operator stack.
                    $stack->push($token);
                    break;
                // if the token is a right bracket (i.e. ")"), then:
                case $token instanceof CloseBracket:
                    yield from $this->popOperators($stack);
                    // pop the left bracket from the stack.
                    $stack->pop();
                    break;
                // if the token is a function argument separator (e.g., a comma), then:
                case $token instanceof Comma:
                    yield from $this->popOperators($stack);
                    break;
                // if the token is an operator, then:
                default:
                   yield from $this->pushOperator($stack, $token);
            }
        }

        yield from $this->clearStack($stack);
    }

    /**
     * @param SplStack<OperatorInterface> $stack
     * @param OperatorInterface $token
     * @return Generator<OperatorInterface>
     */
    private function pushOperator(SplStack $stack, OperatorInterface $token): Generator
    {
        // while there is an operator at the top of the operator stack with
        // greater than or equal to precedence:
        while (!$stack->isEmpty() && $token->lessOrEqual($stack->top())) {
            // pop operators from the operator stack, onto the output queue.
            yield $stack->pop();
        }
        // push the read operator onto the operator stack.
        $stack->push($token);
    }

    /**
     * @param SplStack<OperatorInterface> $stack
     * @return Generator<OperatorInterface>
     * @throws ParseException
     */
    private function popOperators(SplStack $stack): Generator
    {
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
    }

    /**
     * @param SplStack<OperatorInterface> $stack
     * @return Generator<OperatorInterface>
     * @throws ParseException
     */
    private function clearStack(SplStack $stack): Generator
    {
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
