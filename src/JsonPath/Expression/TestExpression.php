<?php

namespace JsonScout\JsonPath\Function\JsonPath\Expression;

use JsonScout\JsonPath\Function\JsonPath\Object\Node;
use JsonScout\JsonPath\Function\JsonPath\Object\LogicalType;



final readonly class TestExpression
    extends AbstractLogicalExpression
{
    //==================================================================================================================
    public function __construct(
        private ITestable $testExpr,
        private bool      $negate
    ) {}
    
    //==================================================================================================================
    #[\Override]
    public function evaluate(Node $root, Node $current)
        : LogicalType
    {
        $eval = $this->testExpr->test($root, $current);
        return ($this->negate ? $eval->negate() : $eval);
    }
}