<?php

namespace JsonScout\JsonPath\Function\JsonPath\Expression;

use JsonScout\JsonPath\Function\JsonPath\Expression\Selector\ChildSelector;
use JsonScout\JsonPath\Function\JsonPath\Object\Node;
use JsonScout\JsonPath\Function\JsonPath\Object\Location;
use JsonScout\JsonPath\Function\JsonPath\Object\NodesType;
use JsonScout\JsonPath\Function\JsonPath\Expression\Selector\ISegmentSelector;



final readonly class SegmentExpression
{
    //==================================================================================================================
    private static function recurseNodes(NodesType $nodes)
        : NodesType
    {
        $queue  = array_filter($nodes->nodes, function($node) { return $node->isCollection(); });
        $result = $queue;

		while (count($queue) > 0)
		{
			$node = reset($queue);
			unset($queue[key($queue)]);

			$value = (array) $node->value;
			
			foreach ($value as $key => $child_value)
			{
				$new_node = new Node(new Location($key, $node), $child_value);
				$result[] = $new_node;

				if ($new_node->isCollection())
				{
					$queue[] = $new_node;
				}
			}
		}

        return new NodesType($result);
    }

    //==================================================================================================================
    public bool $singular;

    //==================================================================================================================
    /** @param ISegmentSelector[] $selectors */
    public function __construct(
        private array $selectors,
        private bool  $recursive
    )
    {
        $this->singular = (count($this->selectors) === 1 && $this->selectors[0] instanceof ChildSelector);
    }
    
    //==================================================================================================================
    public function evaluate(Node $root, NodesType $context)
        : NodesType
    {
        $result = [];

        if ($this->recursive)
        {
            $context = self::recurseNodes($context);
        }
        
        foreach ($this->selectors as $selector)
        {
            array_push($result, ...$selector->select($root, $context)->nodes);
        }

        return new NodesType($result);
    }
}
