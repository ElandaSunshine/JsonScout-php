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
use JsonScout\JsonPath\Object\LogicalType;
use JsonScout\JsonPath\Object\Nothing;
use JsonScout\JsonPath\Object\ValueType;



class TypeExtension
    implements IExtensionProvider
{
    //==================================================================================================================
    #[\Override]
    public function createExtension()
        : array
    {
        /** @phpstan-ignore return.type */
        return [
            'array'  => [ self::class, 'array'  ],
            'object' => [ self::class, 'object' ],
            'string' => [ self::class, 'string' ],
            'number' => [ self::class, 'number' ],
            'bool'   => [ self::class, 'bool'   ],
            'typeof' => [ self::class, 'typeof' ],
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
    public static function string(ValueType $value)
        : ValueType
    {
        $val = $value->value;
        
        if (is_string($val))
        {
            return $value;
        }
        
        if (is_float($val) || is_int($val))
        {
            return new ValueType(''.$val);
        }
        
        if (is_bool($val))
        {
            return new ValueType($val ? 'true' : 'false');
        }
        
        if (is_array($val) || $val instanceof \stdClass)
        {
            return new ValueType(json_encode($val));
        }
        
        if ($val === null)
        {
            return new ValueType('null');
        }
        
        return $value;
    }
    
    public static function number(ValueType $value)
        : ValueType
    {
        $val = $value->value;

        if (is_float($val) || is_int($val))
        {
            return new ValueType($val);
        }
        
        if (is_array($val) || $val instanceof \stdClass)
        {
            return new ValueType(0);
        }
        
        if ($val !== Nothing::NoValue)
        {
            return new ValueType((float) $val);
        }
        
        return $value;
    }
    
    public static function bool(ValueType $value)
        : ValueType
    {
        $val = $value->value;

        if (is_string($val))
        {
            return new ValueType($val !== '');
        }
        
        if ($val !== Nothing::NoValue)
        {
            return new ValueType((bool) $val);
        }
        
        return $value;
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
