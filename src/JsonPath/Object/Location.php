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
	/** @var array<int|string> */
	public array $segments;

	//==================================================================================================================
	public function __construct(string|int $current, Location|Node|null $parent)
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

		$this->segments = [ ...$segments, $current ];
	}

	//==================================================================================================================
	#[\Override]
	public function __toString()
		: string
	{
		$result = "$";

		foreach (array_slice($this->segments, 1) as $segment)
		{
			$result .= self::getNormalisedName($segment);
		}

		return $result;
	}
}
