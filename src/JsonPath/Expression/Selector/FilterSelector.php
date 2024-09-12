<?php

namespace JsonScout\JsonPath\Function\JsonPath\Expression\Selector;

use JsonScout\JsonPath\Function\JsonPath\Expression\AbstractLogicalExpression;
use JsonScout\JsonPath\Function\JsonPath\Object\Node;
use JsonScout\JsonPath\Function\JsonPath\Object\Location;
use JsonScout\JsonPath\Function\JsonPath\Object\LogicalType;
use JsonScout\JsonPath\Function\JsonPath\Object\NodesType;



final readonly class FilterSelector
    implements ISegmentSelector
{
    //==================================================================================================================
    public function __construct(
        private AbstractLogicalExpression $evaluable
    ) {}

    //==================================================================================================================
    #[\Override]
    public function select(Node $root, NodesType $context)
        : NodesType
    {
        $result = [];

        foreach ($context->nodes as $node)
        {
            if (!$node->isCollection())
            {
                continue;
            }

            foreach ((array) $node->value as $key => $child_value)
            {
                $new_node = new Node(new Location($key, $node), $child_value);
                $eval     = $this->evaluable->evaluate($root, $new_node);

                if ($eval === LogicalType::True)
                {
                    $result[] = $new_node;
                }
            }
        }

        return new NodesType($result);
    }
}
