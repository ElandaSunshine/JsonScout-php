<?php

namespace JsonScout\JsonPath\Function\JsonPath\Function;

use JsonScout\JsonPath\Function\JsonPath\Object\LogicalType;
use JsonScout\JsonPath\Function\JsonPath\Object\NodesType;
use JsonScout\JsonPath\Function\JsonPath\Object\ValueType;



class BuiltinExtensions
{
    public static function length(ValueType $value)
        : ValueType
    {
        $val = $value->value;

        if (is_string($val))
        {
            return new ValueType(mb_strlen($val));
        }

        if (is_array($val) || ($val instanceof \stdClass))
        {
            return new ValueType(count((array) $val));
        }

        return new ValueType();
    }

    public static function count(NodesType $nodes)
        : ValueType
    {
        return new ValueType(count($nodes->nodes));
    }
    
    public static function match(ValueType $input, ValueType $pattern)
        : LogicalType
    {
        if (!is_string($input->value) || !is_string($pattern->value))
        {
            return LogicalType::False;
        }
        
        return LogicalType::fromBool(preg_match('/^'.$pattern->value.'$/', $input->value) !== false);
    }
    
    public static function search(ValueType $input, ValueType $pattern)
        : LogicalType
    {
        if (!is_string($input->value) || !is_string($pattern->value))
        {
            return LogicalType::False;
        }
        
        return LogicalType::fromBool(preg_match('/'.$pattern->value.'/', $input->value) !== false);
    }
    
    public static function value(NodesType $nodes)
        : ValueType
    {
        if (count($nodes->nodes)=== 1)
        {
            return new ValueType($nodes->nodes[0]->value);
        }
        
        return new ValueType();
    }
}
