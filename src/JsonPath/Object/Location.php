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
 * Represents the location of a node inside of a query argument.
 * @link https://elandasunshine.github.io/wiki?page=JsonScout/types/Location%23lang-php
 */
final readonly class Location
	implements \Stringable
{
	//==================================================================================================================
	private static function getNormalisedName(int|string $key)
		: string
	{
		if (is_string($key))
		{
			$key = str_replace('\'', '\\\'', $key);
			return "['$key']";
		}

		return "[$key]";
	}

	//==================================================================================================================
	/**
	 * @var array<int|string> $segments The list of child segments this location is comprised of
	 */
	public array $segments;

	//==================================================================================================================
	/**
	 * Constructs a new Location object.
	 * 
 	 * @link https://elandasunshine.github.io/wiki?page=JsonScout/types/Location/Location%23lang-php
	 * 
	 * @param string|int $key The key of the current child in the parent
	 * @param Location|Node|null $parent The parent the node is a child of (either a Location or a Node), or null if
	 * it is the root object
	 */
	public function __construct(string|int $key, Location|Node|null $parent)
	{
		$segments = [];
		
		if ($parent instanceof Location)
		{
			array_push($segments, ...$parent->segments);
		}
		else if ($parent instanceof Node)
		{
			array_push($segments, ...$parent->location->segments);
		}

		$this->segments = [ ...$segments, $key ];
	}

	//==================================================================================================================
	/**
	 * Returns the normalised query string computed from the segments of this location object.
 	 * @link https://elandasunshine.github.io/wiki?page=JsonScout/types/Location/toString%23lang-php
	 * @return string The normalised query string
	 */
	public function toString()
		: string
	{
		$result = "$";

		foreach (array_slice($this->segments, 1) as $segment)
		{
			$result .= self::getNormalisedName($segment);
		}

		return $result;
	}

	//==================================================================================================================
	#[\Override]
	public function __toString() : string { return $this->toString(); }
}
