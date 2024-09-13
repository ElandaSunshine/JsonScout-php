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



class StringExtension
{
    #[ExtensionFunction]
    public static function contains(ValueType $value, ValueType $search)
        : LogicalType
    {
        $val = $value->value;
        $s   = $search->value;

        if (is_string($val) && is_string($s))
        {
            return LogicalType::fromBool(str_contains($val, $s));
        }

        return LogicalType::False;
    }

    #[ExtensionFunction]
    public static function starts_with(ValueType $value, ValueType $search)
        : LogicalType
    {
        $val = $value->value;
        $s   = $search->value;

        if (is_string($val) && is_string($s))
        {
            return LogicalType::fromBool(str_starts_with($val, $s));
        }

        return LogicalType::False;
    }

    #[ExtensionFunction]
    public static function ends_with(ValueType $value, ValueType $search)
        : LogicalType
    {
        $val = $value->value;
        $s   = $search->value;

        if (is_string($val) && is_string($s))
        {
            return LogicalType::fromBool(str_ends_with($val, $s));
        }

        return LogicalType::False;
    }

    #[ExtensionFunction]
    public static function from(ValueType $value)
        : ValueType
    {
        $val = $value->value;

        if (is_string($val))
        {
            return $value;
        }

        if (is_numeric($val))
        {
            return new ValueType((string) $val);
        }

        if (is_bool($val))
        {
            return new ValueType($val ? 'true' : 'false');
        }

        if ($val === null)
        {
            return new ValueType('null');
        }

        return new ValueType();
    }

    #[ExtensionFunction]
    public static function to_lower(ValueType $value)
        : ValueType
    {
        $val = $value->value;

        if (is_string($val))
        {
            return new ValueType(strtolower($val));
        }

        return new ValueType();
    }

    #[ExtensionFunction]
    public static function to_upper(ValueType $value)
        : ValueType
    {
        $val = $value->value;

        if (is_string($val))
        {
            return new ValueType(strtoupper($val));
        }

        return new ValueType();
    }

    #[ExtensionFunction]
    public static function concat(ValueType $value, ValueType ...$elements)
        : ValueType
    {
        $parts  = [ $value, ...$elements ];
        $result = "";

        foreach ($parts as $part)
        {
            $val = $part->value;

            if (is_string($val))
            {
                $result .= $val;
            }
        }

        return new ValueType($result);
    }
}
