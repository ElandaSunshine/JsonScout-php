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
 * Represents a value mapped to a location inside of a query argument.
 * @link https://elandasunshine.github.io/wiki?page=JsonScout/types/Node%23lang-php
 */
final readonly class Node
{
	//==================================================================================================================
	/**
     * Constructs a new Node object.
     * 
     * @link https://elandasunshine.github.io/wiki?page=JsonScout/types/Node%23lang-php
     * 
	 * @param Location  $location The location of this node inside the query argument
	 * @param JSONValue $value    The JSON value object
	 */
	public function __construct(
        public Location $location,
        public mixed    $value
    ) {}

	//==================================================================================================================
	/**
	 * Determines whether this node represents a structured data element.
     * @link https://elandasunshine.github.io/wiki?page=JsonScout/types/Node/isCollection%23lang-php
	 * @return bool True if the contained value is either a JSON array or JSON object
	 */
	public function isCollection() : bool { return ($this->isArray() || $this->isMap()); }

    /**
     * Determines whether this node represents a JSON array.
     * @link https://elandasunshine.github.io/wiki?page=JsonScout/types/Node/isArray%23lang-php
     * @return bool True if the contained value is a JSON array
     */
    public function isArray() : bool { return is_array($this->value); }

    /**
     * Determines whether this node represents a JSON map.
     * @link https://elandasunshine.github.io/wiki?page=JsonScout/types/Node/isMap%23lang-php
     * @return bool True if the contained value is a JSON map
     */
    public function isMap() : bool { return ($this->value instanceof \stdClass); }

    /**
     * Determines whether this node represents a JSON number (int or float).
     * @link https://elandasunshine.github.io/wiki?page=JsonScout/types/Node/isNumber%23lang-php
     * @return bool True if the contained value is a JSON number
     */
    public function isNumber() : bool { return (is_float($this->value) || is_int($this->value)); }

    /**
     * Determines whether this node represents a JSON bool.
     * @link https://elandasunshine.github.io/wiki?page=JsonScout/types/Node/isBool%23lang-php
     * @return bool True if the contained value is a JSON bool
     */
    public function isBool() : bool { return is_bool($this->value); }

    /**
     * Determines whether this node represents a JSON string.
     * @link https://elandasunshine.github.io/wiki?page=JsonScout/types/Node/isString%23lang-php
     * @return bool True if the contained value is a JSON string
     */
    public function isString() : bool { return is_string($this->value); }

    /**
     * Determines whether this node represents a JSON null value.
     * @link https://elandasunshine.github.io/wiki?page=JsonScout/types/Node/isNull%23lang-php
     * @return bool True if the contained value is a JSON null
     */
    public function isNull() : bool { return ($this->value === null); }
}
