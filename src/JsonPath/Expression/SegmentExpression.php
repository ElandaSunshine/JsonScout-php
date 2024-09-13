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

use JsonScout\JsonPath\Expression\Selector\ChildSelector;
use JsonScout\JsonPath\Object\Node;
use JsonScout\JsonPath\Object\Location;
use JsonScout\JsonPath\Object\NodesType;
use JsonScout\JsonPath\Expression\Selector\ISegmentSelector;



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
