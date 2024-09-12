<?php

namespace JsonScout\JsonPath\Function;

use RuntimeException;



class ExceptionFunctionExtension
    extends RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
