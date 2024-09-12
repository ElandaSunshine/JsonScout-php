<?php

namespace JsonScout\JsonPath\Function\JsonPath\Parser;

use RuntimeException;



class ExceptionInternalError extends RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct("internal error: $message");
    }
}
