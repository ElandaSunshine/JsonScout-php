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

use JsonScout\JsonPath\Function\ExtensionFunction;
use JsonScout\JsonPath\Object\LogicalType;
use JsonScout\JsonPath\Object\ValueType;



class ArrayExtension
{
    #[ExtensionFunction]
    public static function contains(ValueType $value, ValueType $search)
        : LogicalType
    {
        $val = $value->value;
        $s   = $search->value;

        if (is_array($val) || $val instanceof \stdClass)
        {
            return LogicalType::fromBool(in_array($s, (array) $val, true));
        }

        return LogicalType::False;
    }
}
