<?php

namespace JsonScout\JsonPath\Function\JsonPath\Expression;

use JsonScout\JsonPath\Function\JsonPath\Object\Node;
use JsonScout\JsonPath\Function\JsonPath\Object\LogicalType;



final readonly class NegationExpression
    extends AbstractLogicalExpression
{
    //==================================================================================================================
    public function __construct(
        private AbstractLogicalExpression $evaluable
    ) {}
    
    //==================================================================================================================
    #[\Override]
    public function evaluate(Node $root, Node $current)
        : LogicalType
    {
        $result = $this->evaluable->evaluate($root, $current);
        return $result->negate();
    }
}
