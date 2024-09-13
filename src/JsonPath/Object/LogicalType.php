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

namespace JsonScout\JsonPath\Object;



enum LogicalType
{
    //==================================================================================================================
    /** Represents a distinct "true" value in a JSONPath query. */
    case True;

    /** Represents a distinct "false" value in a JSONPath query. */
    case False;

    //==================================================================================================================
    /**
     * Converts a boolean value to a LogicalType case value, e.g. true => LogicalType::True and vice-versa.
     * @param bool $value The boolean to convert
     * @return self The converted LogicalType
     */
    public static function fromBool(bool $value)
        : self
    {
        return ($value ? self::True : self::False);
    }
    
    //==================================================================================================================
    /**
     * Inverts the current case to the opposite case, e.g. LogicalType::True => LogicalType::False and vice-versa.
     * @return self The negated LogicalType
     */
    public function negate()
        : self
    {
        return match ($this) {
            LogicalType::True  => LogicalType::False,
            LogicalType::False => LogicalType::True
        };
    }

    /**
     * Converts the current case value to its boolean variant, e.g. LogicalType::True => true and vice-versa.
     * @return bool True if LogicalType::True, otherwise false
     */
    public function toBool() : bool { return ($this == LogicalType::True); }
}
