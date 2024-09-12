<?php

namespace JsonScout\JsonPath\Function\JsonPath\Function;

use RuntimeException;



class ExceptionFunctionRegistration
    extends RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
