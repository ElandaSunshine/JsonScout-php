<?php

namespace JsonScout\JsonPath\Function\JsonPath\Expression\Selector;

use JsonScout\JsonPath\Function\JsonPath\Object\Node;
use JsonScout\JsonPath\Function\JsonPath\Object\NodesType;



interface ISegmentSelector
{
    public function select(Node $root, NodesType $context) : NodesType;
}
