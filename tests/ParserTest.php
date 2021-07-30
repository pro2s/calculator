<?php

use PHPUnit\Framework\TestCase;
use Parser\ShuntingYard;
use Parser\Exceptions\ParseException;
use Parser\Exceptions\SyntaxException;
use Parser\Exceptions\RuntimeException;

final class ParserTest extends TestCase
{
    private ShuntingYard $parser;

    public function setUp(): void
    {
        $this->parser = new ShuntingYard();
    }

    /**
     * @dataProvider correctIntProvider
     * @param string $data
     * @param numeric $result
     * @throws ParseException
     * @throws RuntimeException
     */
    public function testIntParse(string $data, $result): void
    {
        $this->assertSame($result, $this->parser->parse($data));
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
            '3*(5-2+1)/2^(1+1)' => ['3 * (5 - 2 + 1) / 2 ^ (1 + 1)', 3],
        ];
    }

    /**
     * @dataProvider incorrectProvider
     */
    public function testError(string $data, string $exception): void
    {
        $this->expectException($exception);
        $this->parser->parse($data);
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
}
