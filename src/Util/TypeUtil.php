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

namespace JsonScout\Util;

use JsonScout\JsonPath\Object\ValueType;

class TypeUtil
{
    //==================================================================================================================
    public static function equalityCompareOperands(ValueType $op1, ValueType $op2)
        : bool
    {
        $lvalue = $op1->value;
        $rvalue = $op2->value;
        
        if (((is_int($lvalue) || is_float($lvalue)) && (is_int($rvalue) || is_float($rvalue)))
            || (($lvalue instanceof \stdClass) && ($rvalue instanceof \stdClass)))
        {
            return ($lvalue == $rvalue);
        }

        return ($lvalue === $rvalue);
    }

    public static function lessCompareOperands(ValueType $op1, ValueType $op2)
        : bool
    {
        $lvalue = $op1->value;
        $rvalue = $op2->value;

        if ((is_int($lvalue) || is_float($lvalue)) && (is_int($rvalue) || is_float($rvalue)))
        {
            return ($lvalue < $rvalue);
        }
        
        if (is_string($lvalue) && is_string($rvalue))
        {
            return (strcmp($lvalue, $rvalue) < 0);
        }

        return false;
    }
}