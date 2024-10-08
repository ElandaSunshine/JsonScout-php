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



/**
 * Holds any valid JSON value type.
 * @link https://elandasunshine.github.io/wiki?page=JsonScout/types/ValueType%23lang-php
 */
final readonly class ValueType
{
    //==================================================================================================================
    /**
     * Constructs a new ValueType object.
     * @link https://elandasunshine.github.io/wiki?page=JsonScout/types/ValueType/ValueType%23lang-php
     * @param JSONValue|Nothing $value The JSON value this object represents,
     * or {@see Nothing} if it doesn't contain a value
     */
    public function __construct(
        public mixed $value = Nothing::NoValue
    ) {}

    //==================================================================================================================
    /**
     * Determines whether this value object has a JSON value or not.
     * @link https://elandasunshine.github.io/wiki?page=JsonScout/types/ValueType/hasValue%23lang-php
     * @return bool True if {@see $value} is of type {@see Nothing::NoValue}
     */
    public function hasValue() : bool { return ($this->value !== Nothing::NoValue); }
}
