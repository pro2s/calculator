<?php

namespace Parser;

interface ParserInterface
{
    /**
     * @return numeric
     */
    public function parse(string $string);
}
