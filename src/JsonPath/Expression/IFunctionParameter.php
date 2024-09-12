<?php

namespace JsonScout\JsonPath\Function\JsonPath\Expression;

use JsonScout\JsonPath\Function\JsonPath\Object\LogicalType;
use JsonScout\JsonPath\Function\JsonPath\Object\Node;
use JsonScout\JsonPath\Function\JsonPath\Object\NodesType;
use JsonScout\JsonPath\Function\JsonPath\Object\ValueType;



interface IFunctionParameter
{
    public function toParameter(string $paramaterType, Node $root, Node $current) : LogicalType|ValueType|NodesType;

    /**
     * @param     class-string $paramaterType
     * @param-out string       $error
     */
    public function validateParameter(string $paramaterType, string &$error) : bool;
}
