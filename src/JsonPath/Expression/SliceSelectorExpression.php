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

namespace JsonScout\JsonPath\Expression;

use JsonScout\JsonPath\Object\Node;
use JsonScout\JsonPath\Object\Location;
use JsonScout\JsonPath\Object\NodesType;



final readonly class SliceSelectorExpression
	implements ISegmentSelector
{
    //==================================================================================================================
    private static function normaliseOrFallback(?int $value, int $len, int $fallback)
        : int
    {
        return ($value !== null ? ($value  >= 0 ? $value  : ($len + $value)) : $fallback);
    }
    
    //==================================================================================================================
    public function __construct(
        private ?int $start,
        private ?int $end,
        private int  $step
	) {}

    //==================================================================================================================
    #[\Override]
	public function process(Node $root, NodesType $context)
        : NodesType
    {
        $result = [];

        foreach ($context->nodes as $node)
		{
			$value = $node->value;

			if (is_array($value))
			{
				$len = count($value);

				if ($len === 0)
				{
					continue;
				}

				if ($this->step > 0)
				{
					$start = self::normaliseOrFallback($this->start, $len, 0);
					$end   = self::normaliseOrFallback($this->end,   $len, $len);
					$lower = min(max($start, 0), $len);
					$upper = min(max($end,   0), $len);

					for ($i = $lower; $i < $upper; $i += $this->step)
					{
						/** @var JSONValue */
						$val = $value[$i];
						$result[] = new Node(new Location($i, $node), $val);
					}
				}
				else
				{
					$start = self::normaliseOrFallback($this->start, $len, ( $len - 1));
					$end   = self::normaliseOrFallback($this->end,   $len, (-$len - 1));
					$lower = min(max($end,   -1), $len - 1);
					$upper = min(max($start, -1), $len - 1);

					for ($i = $upper; $lower < $i; $i += $this->step)
					{
						/** @var JSONValue */
						$val = $value[$i];
						$result[] = new Node(new Location($i, $node), $val);
					}
				}
			}
		}

        return new NodesType($result);
    }
}
