<?php

namespace JsonScout\JsonPath\Expression;

use JsonScout\JsonPath\Object\Node;
use JsonScout\JsonPath\Object\NodesType;



interface IExpression
{
    public function process(Node $root, NodesType $context) : NodesType;
}