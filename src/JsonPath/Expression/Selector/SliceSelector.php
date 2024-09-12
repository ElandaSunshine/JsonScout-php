<?php

namespace JsonScout\JsonPath\Function\JsonPath\Expression\Selector;

use JsonScout\JsonPath\Function\JsonPath\Object\Node;
use JsonScout\JsonPath\Function\JsonPath\Object\Location;
use JsonScout\JsonPath\Function\JsonPath\Object\NodesType;



final readonly class SliceSelector
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
	public function select(Node $root, NodesType $context)
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
