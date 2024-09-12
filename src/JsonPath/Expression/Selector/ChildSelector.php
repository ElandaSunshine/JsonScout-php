<?php

namespace JsonScout\JsonPath\Function\JsonPath\Expression\Selector;

use JsonScout\JsonPath\Function\JsonPath\Object\Node;
use JsonScout\JsonPath\Function\JsonPath\Object\Location;
use JsonScout\JsonPath\Function\JsonPath\Object\NodesType;



final readonly class ChildSelector
    implements ISegmentSelector
{
    //==================================================================================================================
    public function __construct(
        private int|string $key
    ) {}

    //==================================================================================================================
    #[\Override]
    public function select(Node $root, NodesType $context)
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
