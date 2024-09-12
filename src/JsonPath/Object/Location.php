<?php

namespace JsonScout\JsonPath\Function\JsonPath\Object;



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
