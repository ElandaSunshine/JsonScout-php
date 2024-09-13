<?php
/**
 * MIT License
 *
 * Copyright (c) 2024 ElandaSunshine
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * @package   elandasunshine/jsonscout
 * @author    Elanda
 * @copyright 2024 ElandaSunshine
 * @license   https://choosealicense.com/licenses/mit/
 * @since     1.0.0
 * @link      https://github.com/ElandaSunshine/JsonScout_php
 */

namespace JsonScout\JsonPath\Expression;

use JsonScout\JsonPath\Object\LogicalType;
use JsonScout\JsonPath\Object\Node;
use JsonScout\JsonPath\Object\NodesType;
use JsonScout\JsonPath\Object\Nothing;
use JsonScout\JsonPath\Object\ValueType;
use JsonScout\JsonPath\Parser\ExceptionInternalError;



final readonly class QueryExpression
    implements IExpression,
               IComparable,
               ITestable,
               IFunctionParameter
{
    //==================================================================================================================
    /**
     * @var bool Determines whether this query is a singular query, by that means,
     *           was made up of only singular query segments.
     */
    private bool $singular;

    //==================================================================================================================
    /** @param SegmentExpression[] $segments */
    public function __construct(
        private array $segments,
        private bool  $relative
    )
    {
        $singular = true;

        foreach ($segments as $segment)
        {
            if (!$segment->singular)
            {
                $singular = false;
                break;
            }
        }

        $this->singular = $singular;
    }
    
    //==================================================================================================================
    public function process(Node $root, NodesType $context)
        : NodesType
    {        
        foreach ($this->segments as $segment)
        {
            if (count($context->nodes) === 0)
            {
                break;
            }

            $context = $segment->process($root, $context);
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

        $eval = $this->process($root, new NodesType([ ($this->relative ? $current : $root) ]));
        return new ValueType(count($eval->nodes) > 0 ? $eval->nodes[0]->value : Nothing::NoValue);
    }

    #[\Override]
    public function test(Node $root, Node $current)
        : LogicalType
    {
        $eval = $this->process($root, new NodesType([ ($this->relative ? $current : $root) ]));
        return LogicalType::fromBool(count($eval->nodes) > 0);
    }

    #[\Override]
    public function toParameter(string $parameterType, Node $root, Node $current)
        : NodesType|LogicalType|ValueType
    {
        $value = $this->process($root, new NodesType([ ($this->relative ? $current : $root) ]));

        return match($parameterType) {
            ValueType::class   => new ValueType($value->nodes[0]->value),
            LogicalType::class => LogicalType::fromBool(count($value->nodes) > 0),
            NodesType::class   => $value,
            default            => throw new ExceptionInternalError("invalid parametrisation for $parameterType")
        };
    }

    #[\Override]
    public function validateParameter(string $parameterType, string &$error)
        : bool
    {
        if ($parameterType === ValueType::class)
        {
            if (!$this->singular)
            {
                $error = "cannot convert non-singular query to ValueType";
                return false;
            }
        }

        return true;
    }
}
