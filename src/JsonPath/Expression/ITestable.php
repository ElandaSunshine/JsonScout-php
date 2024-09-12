<?php

namespace JsonScout\JsonPath\Function\JsonPath\Expression;

use JsonScout\JsonPath\Function\JsonPath\Object\LogicalType;
use JsonScout\JsonPath\Function\JsonPath\Object\Node;



interface ITestable
{
    public function test(Node $root, Node $current) : LogicalType;
}
