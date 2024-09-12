<?php

namespace JsonScout\JsonPath\Function\JsonPath\Expression\Selector;

use JsonScout\JsonPath\Function\JsonPath\Object\Node;
use JsonScout\JsonPath\Function\JsonPath\Object\Location;
use JsonScout\JsonPath\Function\JsonPath\Object\NodesType;



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
