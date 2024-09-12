<?php

namespace JsonScout\JsonPath\Function\JsonPath\Object;



final readonly class ValueType
{
    //==================================================================================================================
    /** 
     * Constructs a new ValueType object.
     * 
     * If no value is given, this will be an empty value. ({@see JsonScout\Object\Nothing::NoValue})
     * 
     * @param JSONValue|Nothing $value The JSON value this object represents, or empty if it doesn't contain a value
     */
    public function __construct(
        public mixed $value = Nothing::NoValue
    ) {}

    //==================================================================================================================
    /**
     * Determines whether this value object has a JSON value or not.
     * @return bool True if the value is of type {@see JsonScout\Object\Nothing::NoValue}
     */
    public function hasValue() : bool { return ($this->value !== Nothing::NoValue); }
}
