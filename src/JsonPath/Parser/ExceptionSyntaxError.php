<?php

namespace JsonScout\JsonPath\Function\JsonPath\Parser;

use RuntimeException;



class ExceptionSyntaxError extends RuntimeException
{
    public function __construct(string $message, int $line, int $column)
    {
        parent::__construct("$message (line $line:$column)", 0, null);
    }
}
