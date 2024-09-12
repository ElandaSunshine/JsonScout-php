<?php

namespace JsonScout\JsonPath\Function\JsonPath\Expression;

use JsonScout\JsonPath\Function\JsonPath\Object\LogicalType;
use JsonScout\JsonPath\Function\JsonPath\Object\Node;
use JsonScout\JsonPath\Function\JsonPath\Object\NodesType;
use JsonScout\JsonPath\Function\JsonPath\Object\Nothing;
use JsonScout\JsonPath\Function\JsonPath\Object\ValueType;
use JsonScout\JsonPath\Function\JsonPath\Parser\ExceptionInternalError;


final readonly class QueryExpression
    implements IComparable,
               ITestable,
               IFunctionParameter
{
    //==================================================================================================================
    /** @param SegmentExpression[] $segments */
    public function __construct(
        private array $segments,
        private bool  $relative,
        public  bool  $singular
    )
    {}
    
    //==================================================================================================================
    public function evaluate(Node $root, NodesType $context)
        : NodesType
    {        
        foreach ($this->segments as $segment)
        {
            if (count($context->nodes) === 0)
            {
                break;
            }

            $context = $segment->evaluate($root, $context);
        }

        return $context;
    }

    //==================================================================================================================
    #[\Override]
    public function toComparable(Node $root, Node $current)
        : ValueType
    {
        if (!$this->singular)
        {
            throw new ExceptionInternalError("falsely assumed was a singular query");
        }

        $eval = $this->evaluate($root, new NodesType([ ($this->relative ? $current : $root) ]));
        return new ValueType(count($eval->nodes) > 0 ? $eval->nodes[0]->value : Nothing::NoValue);
    }

    #[\Override]
    public function test(Node $root, Node $current)
        : LogicalType
    {
        $eval = $this->evaluate($root, new NodesType([ ($this->relative ? $current : $root) ]));
        return LogicalType::fromBool(count($eval->nodes) > 0);
    }

    #[\Override]
    public function toParameter(string $paramaterType, Node $root, Node $current)
        : NodesType
    {
        return $this->evaluate($root, new NodesType([ ($this->relative ? $current : $root) ]));
    }

    #[\Override]
    public function validateParameter(string $paramaterType, string &$error)
        : bool
    {
        // TODO: Implement validateParameter() method.
    }
}
