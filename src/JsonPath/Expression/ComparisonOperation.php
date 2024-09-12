<?php

namespace JsonScout\JsonPath\Function\JsonPath\Expression;

use Exception;
use JsonScout\JsonPath\Function\JsonPath\Parser\ExceptionInternalError;


enum ComparisonOperation : string
{
    case Equal              = '==';
    case NotEqual           = '!=';
    case LessThan           = '<';
    case LessThanOrEqual    = '<=';
    case GreaterThan        = '>';
    case GreaterThanOrEqual = '>=';
    
    //==================================================================================================================
    public static function fromString(string $op)
        : self
    {
        foreach (self::cases() as $case)
        {
            if ($case->value === $op)
            {
                return $case;
            }
        }
        
        throw new ExceptionInternalError("unimplemented comparison '{$op}'");
    }
}