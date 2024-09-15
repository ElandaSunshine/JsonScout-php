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
use JsonScout\JsonPath\Object\ValueType;



class MathExtension
    implements IExtensionProvider
{
    //==================================================================================================================
    #[\Override]
    public function createExtension()
        : array
    {
        /** @phpstan-ignore return.type */
        return [
            'add' => [ self::class, 'add' ],
            'sub' => [ self::class, 'sub' ],
            'div' => [ self::class, 'div' ],
            'mul' => [ self::class, 'mul' ],
            'min' => [ self::class, 'min' ],
            'max' => [ self::class, 'max' ],
        ];
    }
    
    //==================================================================================================================
    public static function add(ValueType $op1, ValueType $op2)
        : ValueType
    {
        $val1 = $op1->value;
        $val2 = $op2->value;

        if ((is_float($val1) || is_int($val1)) && (is_float($val2) || is_int($val2)))
        {
            return new ValueType($val1 + $val2);
        }
        
        return new ValueType();
    }
    
    public static function sub(ValueType $op1, ValueType $op2)
        : ValueType
    {
        $val1 = $op1->value;
        $val2 = $op2->value;

        if ((is_float($val1) || is_int($val1)) && (is_float($val2) || is_int($val2)))
        {
            return new ValueType($val1 - $val2);
        }
        
        return new ValueType();
    }
    
    public static function div(ValueType $op1, ValueType $op2)
        : ValueType
    {
        $val1 = $op1->value;
        $val2 = $op2->value;

        if ((is_float($val1) || is_int($val1)) && (is_float($val2) || is_int($val2)))
        {
            return new ValueType($val1 / $val2);
        }
        
        return new ValueType();
    }
    
    public static function mul(ValueType $op1, ValueType $op2)
        : ValueType
    {
        $val1 = $op1->value;
        $val2 = $op2->value;

        if ((is_float($val1) || is_int($val1)) && (is_float($val2) || is_int($val2)))
        {
            return new ValueType($val1 * $val2);
        }
        
        return new ValueType();
    }
    
    //==================================================================================================================
    public static function min(ValueType $op1, ValueType $op2)
        : ValueType
    {
        $val1 = $op1->value;
        $val2 = $op2->value;

        if ((is_float($val1) || is_int($val1)) && (is_float($val2) || is_int($val2)))
        {
            return new ValueType(min($val1, $val2));
        }
        
        return new ValueType();
    }
    
    public static function max(ValueType $op1, ValueType $op2)
        : ValueType
    {
        $val1 = $op1->value;
        $val2 = $op2->value;

        if ((is_float($val1) || is_int($val1)) && (is_float($val2) || is_int($val2)))
        {
            return new ValueType(max($val1, $val2));
        }
        
        return new ValueType();
    }
}
