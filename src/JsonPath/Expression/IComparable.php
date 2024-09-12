<?php

namespace JsonScout\JsonPath\Function\JsonPath\Expression;

use JsonScout\JsonPath\Function\JsonPath\Object\Node;
use JsonScout\JsonPath\Function\JsonPath\Object\ValueType;



interface IComparable
{
    public function toComparable(Node $root, Node $current) : ValueType;
}
