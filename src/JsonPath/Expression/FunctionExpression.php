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

use JsonScout\JsonPath\Function\FunctionExtension;
use JsonScout\JsonPath\Object\LogicalType;
use JsonScout\JsonPath\Object\Node;
use JsonScout\JsonPath\Object\NodesType;
use JsonScout\JsonPath\Object\ValueType;
use JsonScout\Util\RefUtil;


final readonly class FunctionExpression
    implements ITestable,
               IComparable,
               IFunctionParameter
{
    //==================================================================================================================
    /**
     * @param array<IFunctionParameter> $arguments
     */
    public function __construct(
        private FunctionExtension $extension,
        private array             $arguments
    ) {}

    //==================================================================================================================
    public function evaluate(Node $root, Node $current)
        : ValueType|LogicalType|NodesType
    {
        $args = [];

        foreach ($this->arguments as $i => $argument)
        {
            $class  = $this->getExpectedClassForArgument($i);
            $args[] = $argument->toParameter($class, $root, $current);
        }

        return $this->extension->invoke(...$args);
    }

    //==================================================================================================================
    #[\Override]
    public function test(Node $root, Node $current)
        : LogicalType
    {
        $result = $this->evaluate($root, $current);
        assert($result instanceof NodesType || $result instanceof LogicalType);

        if ($result instanceof NodesType)
        {
            return LogicalType::fromBool(count($result->nodes) > 0);
        }

        return $result;
    }

    #[\Override]
    public function toComparable(Node $root, Node $current)
        : ValueType
    {
        $result = $this->evaluate($root, $current);
        assert($result instanceof ValueType);

        return $result;
    }

    #[\Override]
    public function toParameter(string $parameterType, Node $root, Node $current)
        : LogicalType|ValueType|NodesType
    {
        $this_type = $this->extension->returnType;
        $value     = $this->evaluate($root, $current);

        if ($parameterType !== $this_type)
        {
            assert($value instanceof NodesType);
            return LogicalType::fromBool(count($value->nodes) > 0);
        }

        return $value;
    }

    #[\Override]
    public function validateParameter(string $parameterType, string &$error): bool
    {
        $this_type = $this->extension->returnType;

        if ($parameterType !== $this_type)
        {
            $unqualified_this_name = RefUtil::getUnqualifiedName($this_type);

            switch ($parameterType)
            {
                case LogicalType::class:
                {
                    if ($this_type !== NodesType::class)
                    {
                        $error = "expected LogicalType (or NodesType) instead got $unqualified_this_name";
                        return false;
                    }
                }

                case ValueType::class:
                {
                    $error = "expected ValueType instead got $unqualified_this_name";
                    return false;
                }

                case NodesType::class:
                {
                    $error = "expected NodesType instead got $unqualified_this_name";
                    return false;
                }
            }
        }

        return true;
    }
    //==================================================================================================================
    public function validForContext(int $context) : bool { return ($this->extension->canBeUsedFor($context)); }
    public function getExtensionName() : string { return $this->extension->extensionName; }
    public function getReturnType() : string { return $this->extension->returnType; }

    //==================================================================================================================
    /**
     * @return class-string
     */
    private function getExpectedClassForArgument(int $index)
        : string
    {
        $ext_params = $this->extension->parameters;

        $param = $ext_params[min($index, (count($ext_params) - 1))];
        assert($param->getType() instanceof \ReflectionNamedType);

        /** @var class-string */
        return $param->getType()->getName();
    }
}
