<?php

namespace JsonScout\JsonPath\Function;

use JsonScout\JsonPath\PathNode;
use RuntimeException;
use Stringable;

class ExceptionReadFailure
    extends RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
