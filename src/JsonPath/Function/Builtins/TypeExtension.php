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

use JsonScout\JsonPath\Function\IExtensionProvider;
use JsonScout\JsonPath\Object\Nothing;
use JsonScout\JsonPath\Object\ValueType;
use JsonScout\Util\RefUtil;

class TypeExtension
    implements IExtensionProvider
{
    //==================================================================================================================
    #[\Override]
    public function createExtension()
        : array
    {
        return [
            'array'  => self::array(...),
            'object' => self::object(...),
            'typeof' => self::typeof(...)
        ];
    }
    
    //==================================================================================================================
    public static function array(ValueType ...$elements)
        : ValueType
    {
        $values = [];
        
        foreach ($elements as $element)
        {
            if ($element->hasValue())
            {
                $values[] = $element->value;
            }
        }

        return new ValueType($values);
    }
    
    public static function object(ValueType ...$pairs)
        : ValueType
    {
        $result = new \stdClass();
        $len    = count($pairs);
        
        for ($i = 0; $i < $len; $i += 2)
        {
            $n = ($i + 1);
            
            if ($n >= $len)
            {
                break;
            }
            
            $key   = $pairs[$i];
            $value = $pairs[$n];
            
            if (!is_string($key->value) || !$value->hasValue())
            {
                continue;
            }
            
            $result->{$key} = $value;
        }

        return new ValueType($result);
    }

    //==================================================================================================================
    public static function typeof(ValueType $value)
        : ValueType
    {
        if (!$value->hasValue())
        {
            return $value;
        }

        $val = $value->value;

        if (is_float($val) || is_int($val))
        {
            return new ValueType('number');
        }

        if ($val instanceof \stdClass)
        {
            return new ValueType('object');
        }

        if ($val === null)
        {
            return new ValueType('null');
        }

        if (is_bool($val))
        {
            return new ValueType('bool');
        }

        return new ValueType(gettype($value->value));
    }
}
