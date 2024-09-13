<?php
/**
 * MIT License
 *
 * Copyright (c) 2024 ElandaSunshine
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * @package   elandasunshine/jsonscout
 * @author    Elanda
 * @copyright 2024 ElandaSunshine
 * @license   https://choosealicense.com/licenses/mit/
 * @since     1.0.0
 * @link      https://github.com/ElandaSunshine/JsonScout_php
 */

namespace JsonScout\JsonPath\Function\Builtins;

use JsonScout\JsonPath\Object\LogicalType;
use JsonScout\JsonPath\Object\NodesType;
use JsonScout\JsonPath\Object\ValueType;



class StandardExtension
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
        
        return LogicalType::fromBool(preg_match('/^'.$pattern->value.'$/', $input->value) !== 0);
    }
    
    public static function search(ValueType $input, ValueType $pattern)
        : LogicalType
    {
        if (!is_string($input->value) || !is_string($pattern->value))
        {
            return LogicalType::False;
        }

        return LogicalType::fromBool(preg_match('/'.$pattern->value.'/', $input->value) !== 0);
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
