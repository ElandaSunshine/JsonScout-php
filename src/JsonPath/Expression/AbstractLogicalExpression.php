<?php

namespace JsonScout\JsonPath\Function\JsonPath\Expression;

use JsonScout\JsonPath\Function\JsonPath\Object\LogicalType;
use JsonScout\JsonPath\Function\JsonPath\Object\Node;



abstract readonly class AbstractLogicalExpression
    implements IFunctionParameter
{
    public abstract function evaluate(Node $root, Node $current) : LogicalType;

    #[\Override]
    public function parameterise(Node $root, Node $current) : LogicalType { return $this->evaluate($root, $current); }
}
