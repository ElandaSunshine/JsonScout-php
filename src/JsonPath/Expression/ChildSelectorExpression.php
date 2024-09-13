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



final readonly class ChildSelectorExpression
    implements ISegmentSelector
{
    //==================================================================================================================
    public function __construct(
        private int|string $key
    ) {}

    //==================================================================================================================
    #[\Override]
    public function process(Node $root, NodesType $context)
        : NodesType
    {
        $result = [];
        $key    = $this->key;

        if (is_int($key))
        {
            foreach ($context->nodes as $node)
            {
                $value = $node->value;

                if (is_array($value))
                {
                    $len = count($value);

                    if ($key < 0)
                    {
                        $key += $len;
                    }

                    if ($key >= 0 && $key < $len)
                    {
                        // @phpstan-ignore argument.type
                        $result[] = new Node(new Location($key, $node), $value[$key]);
                    }
                }
            }
        }
        else
        {
            foreach ($context->nodes as $node)
            {
                $value = $node->value;

                if (($value instanceof \stdClass) && property_exists($value, $key))
                {
                    $result[] = new Node(new Location($key, $node), $value->{$key});
                }
            }
        }

        return new NodesType($result);
    }
}
