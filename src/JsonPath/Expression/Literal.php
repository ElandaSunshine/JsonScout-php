<?php

namespace JsonScout\JsonPath\Function\JsonPath\Expression;

use JsonScout\JsonPath\Function\JsonPath\Object\Node;
use JsonScout\JsonPath\Function\JsonPath\Object\ValueType;



final readonly class Literal
    implements IComparable,
               IFunctionParameter
{
    //==================================================================================================================
    public function __construct(
        private ValueType $value
    ) {}
    
    //==================================================================================================================
    #[\Override]
    public function toComparable(Node $root, Node $current) : ValueType { return $this->value; }

    #[\Override]
    public function parameterise(Node $root, Node $current) : ValueType { return $this->toComparable($root, $current); }
}
