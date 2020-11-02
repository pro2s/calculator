<?php

namespace Parser\Tests;

use PHPUnit\Framework\TestCase;
use Parser\ShuntingYard;
use Parser\Exceptions\ParseException;
use Parser\Exceptions\SyntaxException;
use Parser\Exceptions\RuntimeException;

class ParserTest extends TestCase
{
    private $parser;

    public function setUp(): void
    {
        $this->parser = new ShuntingYard();
    }

    /**
     * @dataProvider correctIntProvider
     */
    public function testIntParse($data, $result)
    {
        $this->assertSame($result, $this->parser->parse($data));
    }

    public function correctIntProvider()
    {
        return [
            '1+12' => ['1+12', 13],
            '1+1' => ['1+1', 2],
            '2-1' => ['2-1', 1],
            '2*2' => ['2*2', 4],
            '4/2' => ['4/2', 2],
            '6%4' => ['6%4', 2],
            '5^2' => ['5^2', 25],
            'min' => ['1+min(2,5)',3],
            '2*(1+2)' => ['2*(1+2)', 6],
            '3*(5-2+1)/2^(1+1)' => ['3 * (5 - 2 + 1) / 2 ^ (1 + 1)', 3],
        ];
    }

    /**
     * @dataProvider incorrectProvider
     */
    public function testErorr($data, $exception)
    {
        $this->expectException($exception);
        $this->parser->parse($data);
    }

    public function incorrectProvider()
    {
        return [
            'SyntaxException' => ['1$1', SyntaxException::class],
            'ParseException' => ['2((1', ParseException::class],
            'RuntimeException' => ['2/0', RuntimeException::class],
            'RuntimeException' => ['2 ^ (1 + )', RuntimeException::class],
        ];
    }
}
