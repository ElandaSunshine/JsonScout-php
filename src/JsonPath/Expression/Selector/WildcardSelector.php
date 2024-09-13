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

namespace JsonScout\JsonPath\Expression\Selector;

use JsonScout\JsonPath\Object\Node;
use JsonScout\JsonPath\Object\Location;
use JsonScout\JsonPath\Object\NodesType;



final readonly class WildcardSelector
    implements ISegmentSelector
{
    //==================================================================================================================
    public function select(Node $root, NodesType $context)
        : NodesType
    {
        $result = [];

        foreach ($context->nodes as $node)
        {
            if ($node->isCollection())
            {
                foreach ((array) $node->value as $key => $child_value)
                {
                    $result[] = new Node(new Location($key, $node), $child_value);
                }
            }
        }

        return new NodesType($result);
    }
}
