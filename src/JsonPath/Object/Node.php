<?php

namespace JsonScout\JsonPath\Function\JsonPath\Object;



final readonly class Node
{
	//==================================================================================================================
	/**
	 * @param Location  $location The normalised JSONPath query, representing the location of this node starting from root
	 * @param JSONValue $value    The JSON like PHP object this node contains
	 */
	public function __construct(
        public Location $location,
        public mixed    $value
    ) {}

	//==================================================================================================================
	/**
	 *  Determines whether this node represents a structured data element.
	 *  @return bool True if the contained value is either a JSON array or JSON object
	 */
	public function isCollection() : bool { return ($this->isArray() || $this->isMap()); }

    /**
     *  Determines whether this node represents a JSON array.
     *  @return bool True if the contained value is a JSON array
     */
    public function isArray() : bool { return is_array($this->value); }

    /**
     *  Determines whether this node represents a JSON map.
     *  @return bool True if the contained value is a JSON map
     */
    public function isMap() : bool { return ($this->value instanceof \stdClass); }

    /**
     *  Determines whether this node represents a JSON number (int or float).
     *  @return bool True if the contained value is a JSON number
     */
    public function isNumber() : bool { return (is_float($this->value) || is_int($this->value)); }

    /**
     *  Determines whether this node represents a JSON bool.
     *  @return bool True if the contained value is a JSON bool
     */
    public function isBool() : bool { return is_bool($this->value); }

    /**
     *  Determines whether this node represents a JSON string.
     *  @return bool True if the contained value is a JSON string
     */
    public function isString() : bool { return is_string($this->value); }

    /**
     *  Determines whether this node represents a JSON null.
     * 
     *  Note that this is different from {@see JsonScout\Object\ValueType::hasValue()} in that this represents only
     *  the JSON null value, if this is true this ValueType instance still has a value.
     * 
     *  @return bool True if the contained value is a JSON null
     */
    public function isNull() : bool { return ($this->value === null); }
}
