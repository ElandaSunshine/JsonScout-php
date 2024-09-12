<?php

namespace JsonScout\JsonPath\Function\JsonPath\Parser;

use JsonScout\JsonPath\Function\JsonPath\Expression\QueryExpression;
use JsonScout\JsonPath\Function\JsonPath\Object\Location;
use JsonScout\JsonPath\Function\JsonPath\Object\Node;
use JsonScout\JsonPath\Function\JsonPath\Object\NodesType;



final readonly class JsonPathQuery
{
    //==================================================================================================================
    public function __construct(
        private QueryExpression $expression
    ) {}
    
    //==================================================================================================================
    /** @param JSONValue $data */
    public function execute(mixed $data)
        : NodesType
    {
        $root_node = new Node(new Location('$', null), $data);
        return $this->expression->evaluate($root_node, new NodesType([ $root_node ]));
    }
}