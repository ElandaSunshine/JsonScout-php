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

namespace JsonScout\JsonPath\Function;

use JsonScout\JsonPath\Object\LogicalType;
use JsonScout\JsonPath\Object\NodesType;
use JsonScout\JsonPath\Object\ValueType;
use Technically\CallableReflection\CallableReflection;



final readonly class FunctionExtension
{
    //==================================================================================================================
    public const int CONTEXT_ARGUMENT   = 1;
    public const int CONTEXT_COMPARISON = 2;
    public const int CONTEXT_TEST       = 4;

    //==================================================================================================================
    private static function validateType(string $element, string $extName, ?\ReflectionType $type)
        : void
    {
        if ($type === null)
        {
            $error = "invalid $element for function extension '$extName', "
                    ."must have an explicitly specified type of either ValueType, LogicalType or NodesType";
        }
        else if ($type instanceof \ReflectionIntersectionType || $type instanceof \ReflectionUnionType)
        {
            $error = "invalid $element for function extension '$extName', "
                    ."union and intersection types are not supported";
        }
        else
        {
            assert($type instanceof \ReflectionNamedType);

            if (!in_array($type->getName(), [ ValueType::class, LogicalType::class, NodesType::class ], true))
            {
                $error = "invalid $element for function extension '$extName', "
                    ."found type '{$type->getName()}' but expected either ValueType, LogicalType or NodesType";
            }
            else
            {
                return;
            }
        }

        throw new ExceptionFunctionRegistration($error);
    }

    /**
     * @throws \ReflectionException
     */
    private static function getReflector(callable $callable)
        : \ReflectionFunctionAbstract
    {
        $reflection = CallableReflection::fromCallable($callable);
        $refl       = new \ReflectionObject($reflection);

        $prop   = $refl->getProperty('reflector');
        $result = $prop->getValue($reflection);
        assert($result instanceof \ReflectionFunctionAbstract);

        return $result;
    }

    //==================================================================================================================
    /**
     * @var callable $callable
     */
    private mixed $callable;
    private int   $applicableContexts;
    
    /**
     * @var \ReflectionParameter[] $parameters The reflection parameters of this function extension
     */
    public array $parameters;

    /**
     * @var class-string $returnType The return type class of this function extension
     */
    public string $returnType;

    //==================================================================================================================
    /**
     * @param non-empty-string $extensionName
     * @param-later-invoked-callable $callable
     */
    public function __construct(
        public string   $extensionName,
               callable $callable
    )
    {
        if (preg_match('/[a-z][a-z0-9_]+/', $extensionName) === false)
        {
            throw new ExceptionFunctionRegistration(
                "invalid function extension name '$extensionName', must start with a lower-case letter,"
                ."at least one character long and can only contain lower-case letters, digits und underscores"
            );
        }

        try
        {
            $refl = self::getReflector($callable);
        }
        catch (\Exception $ex)
        {
            throw new ExceptionFunctionRegistration($ex->getMessage());
        }

        $pars = [];

        foreach ($refl->getParameters() as $parameter)
        {
            self::validateType("parameter '{$parameter->getName()}'", $extensionName, $parameter->getType());
            $pars[] = $parameter;
        }

        $type = $refl->getReturnType();
        self::validateType("return type", $extensionName, $type);
        assert($type instanceof \ReflectionNamedType);

        /** @var class-string $name */
        $name     = $type->getName();
        $contexts = 0b0111;

        if (in_array($name, [ NodesType::class, LogicalType::class ], true))
        {
            $contexts &= ~self::CONTEXT_COMPARISON;
        }
        else if ($name === ValueType::class)
        {
            $contexts &= ~self::CONTEXT_TEST;
        }

        $this->callable           = $callable;
        $this->applicableContexts = $contexts;
        $this->returnType         = $name;
        $this->parameters         = $pars;
    }

    //==================================================================================================================
    public function canBeUsedFor(int $flag) : bool { return (($this->applicableContexts & $flag) == $flag); }

    //==================================================================================================================
    public function invoke(ValueType|NodesType|LogicalType ...$arguments)
        : ValueType|LogicalType|NodesType
    {
        return ($this->callable)(...$arguments);
    }
}
