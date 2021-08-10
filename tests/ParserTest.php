<?php

use Parser\Calculators\CalculatorInterface;
use Parser\Operands\DecimalFactory;
use Parser\Operands\OperandFactoryInterface;
use Parser\Parser\ParserInterface;
use PHPUnit\Framework\TestCase;
use Parser\Exceptions\ParseException;
use Parser\Exceptions\SyntaxException;
use Parser\Exceptions\RuntimeException;

abstract class ParserTest extends TestCase
{
    protected ?ParserInterface $parser;
    protected ?CalculatorInterface $calculator;

    abstract function setupParser(OperandFactoryInterface $operandFactory): void;

    public function setUp(): void
    {
        $this->setupParser(new DecimalFactory());
    }

    /**
     * @dataProvider correctIntProvider
     * @param string $data
     * @param numeric $result
     */
    public function testIntParse(string $data, $result): void
    {
        $value = $this->calculate($data);
        $this->assertSame($result, $value);
    }

    public function correctIntProvider(): array
    {
        return [
            '1+12' => ['1+12', 13],
            '1+1' => ['1+1', 2],
            '2-1' => ['2-1', 1],
            '2*2' => ['2*2', 4],
            '4/2' => ['4/2', 2],
            '6%4' => ['6%4', 2],
            '5^2' => ['5^2', 25],
            'sqrt' => ['2+sqrt(9)', 5.0],
            'min' => ['2+min(1+1,5)+0',4],
            '2*(1+2)' => ['2*(1+2)', 6],
            '2*3+2' => ['2*3+2', 8],
            '3*(5-2+1)/2^(1+1)' => ['3 * (5 - 2 + 1) / 2 ^ (1 + 1)', 3],
        ];
    }

    /**
     * @dataProvider incorrectProvider
     */
    public function testError(string $data, string $exception): void
    {
        $this->expectException($exception);
        $this->calculate($data);
    }

    public function incorrectProvider(): array
    {
        return [
            'SyntaxException' => ['1$1', SyntaxException::class],
            'ParseException' => ['2((1', ParseException::class],
            'RuntimeException' => ['2/0', RuntimeException::class],
            'RuntimeException brackets' => ['2 ^ (1 + )', RuntimeException::class],
        ];
    }

    /**
     * @param string $data
     * @return float|int|string
     */
    public function calculate(string $data)
    {
        $tokens = $this->parser->parse($data);
        return $this->calculator->calculate($tokens);
    }
}
