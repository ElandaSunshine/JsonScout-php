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

use JsonScout\JsonPath\Function\ExceptionFunctionExtension;
use JsonScout\JsonPath\Function\FunctionExtension;
use JsonScout\JsonPath\Function\FunctionRegistry;
use JsonScout\JsonPath\Object\Location;
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
    /** @param IFunctionParameter[] $array */
    private static function popFront(array &$array)
        : IFunctionParameter|false
    {
        $result = reset($array);

        if ($result !== false)
        {
            unset($array[key($array)]);
        }

        return $result;
    }

    private static function validateArgument(\ReflectionParameter $parameter,
                                             IFunctionParameter   $argument,
                                             FunctionExtension    $ext,
                                             int                  $index)
        : void
    {
        assert($parameter->getType() instanceof \ReflectionNamedType);

        /** @var class-string $type_name */
        $type_name = $parameter->getType()->getName();
        assert(in_array($type_name, [ ValueType::class, LogicalType::class, NodesType::class ], true));

        $error = "";

        if (!$argument->validateParameter($type_name, $error))
        {
            throw new ExceptionFunctionExtension(
                "invalid function call to '$ext->extensionName' at parameter #$index ({$parameter->getName()}), ".$error
            );
        }
    }

    //==================================================================================================================
    public FunctionExtension $extension;
    
    //------------------------------------------------------------------------------------------------------------------
    private ValueType|LogicalType|NodesType|null $cache;

    //==================================================================================================================
    /**
     * @param array<IFunctionParameter> $arguments
     */
    public function __construct(
        private array  $arguments,
                string $extensionName
    )
    {
        $registry  = FunctionRegistry::getInstance();
        $extension = $registry->getExtension($extensionName);

        if ($extension === null)
        {
            throw new ExceptionFunctionExtension("no function extension with name '$extensionName' was registered");
        }

        $temp_args = $arguments;

        foreach ($extension->parameters as $i => $parameter)
        {
            $ctx = self::popFront($temp_args);

            if ($parameter->isVariadic())
            {
                while ($ctx !== false)
                {
                    self::validateArgument($parameter, $ctx, $extension, $i);
                    $ctx = self::popFront($temp_args);
                }
            }
            else
            {
                if ($ctx === false)
                {
                    if (!$parameter->isDefaultValueAvailable())
                    {
                        $needs = count(
                            array_filter($extension->parameters, function($param)
                            {
                                return !$param->isDefaultValueAvailable() && !$param->isVariadic();
                            })
                        );

                        throw new ExceptionFunctionExtension(
                            "too few arguments to function extension '{$extensionName}', "
                            ."expected at least $needs but received only ".count($arguments)
                        );
                    }

                    break;
                }

                self::validateArgument($parameter, $ctx, $extension, $i);
            }
        }

        $this->extension = $extension;
        $this->cache     = $this->precompile();
    }

    //==================================================================================================================
    public function evaluate(Node $root, Node $current)
        : ValueType|LogicalType|NodesType
    {
        if ($this->cache !== null)
        {
            return $this->cache;
        }
        
        return $this->evaluateHelper($root, $current);
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
    public function isPrecompiled() : bool { return ($this->cache !== null); }
    
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
    
    private function precompile()
        : ValueType|LogicalType|NodesType|null
    {
        $should_compile = true;
        
        foreach ($this->arguments as $arg)
        {
            if (!($arg instanceof Literal)
                && (!($arg instanceof FunctionExpression) || $arg->cache === null))
            {
                $should_compile = false;
                break;
            }
        }
        
        $empty = new Node(new Location('', null), null);
        return ($should_compile ? $this->evaluateHelper($empty, $empty) : null);
    }

    private function evaluateHelper(Node $root, Node $current)
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
}
