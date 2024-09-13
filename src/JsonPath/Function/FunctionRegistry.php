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



/**
 * @phpstan-import-type FunctionExtensionCallable from FunctionExtension
 * 
 * Manages a global list of function extensions for JSONPath queries.
 * 
 * Every function extension consists of a name that is also the name of the function inside a query and a
 * specification compliant callable.
 * 
 * A function extension is said to comply with the specification if its return type and parameter types are
 * explicitly specified and is any of {@see ValueType}, {@see NodesType}
 * or {@see LogicalType}.
 * Union and intersection types are not allowed, however, variadic argument lists are.
 * 
 * Furthermore, a function extension can not have side effects, that is, it cannot temporarily store and retrieve data
 * that it acts upon, basically, it should, with the same input, output the same result with every call.
 */
final class FunctionRegistry
{
    //==================================================================================================================
    private static ?self $instance = null;

    //==================================================================================================================
    /**
     * Gets the function extension registry instance.
     * @return self The current registry instance 
     */
    public static function getInstance()
        : self
    {
        if (self::$instance === null)
        {
            self::$instance = new self();
        }
        
        return self::$instance;
    }

    //==================================================================================================================
    /** 
     * @var array<non-empty-string,FunctionExtension>
     */
    private array $extensions = [];

    //==================================================================================================================
    private function __construct()
    {
        // Standard extensions
        $this->registerExtension('length', [ BuiltinExtensions::class, 'length' ]);
        $this->registerExtension('count',  [ BuiltinExtensions::class, 'count'  ]);
        $this->registerExtension('match',  [ BuiltinExtensions::class, 'match'  ]);
        $this->registerExtension('search', [ BuiltinExtensions::class, 'search' ]);
        $this->registerExtension('value',  [ BuiltinExtensions::class, 'value'  ]);
    }

    //==================================================================================================================
    /**
     * Tries to register a new function extension that complies with the specification.
     * 
     * @param non-empty-string          $name              The unique name for the function extension
     * @param FunctionExtensionCallable $functionExtension The callable that should get executed
     * 
     * @throws ExceptionFunctionRegistration Thrown if a function extension by that name already exists or if the
     * function extension doesn't comply with the specification.
     * 
     * @param-later-invoked-callable $functionExtension
     */
    public function registerExtension(string $name, callable $functionExtension)
        : void
    {
        if (array_key_exists($name, $this->extensions))
        {
            throw new ExceptionFunctionRegistration("function extension with the name '$name' already registered");
        }

        $this->extensions[$name] = new FunctionExtension($name, $functionExtension);
    }

    /**
     * Gets a function extension by name or null if no such function extension exists.
     * @param string $name The name of the function extension
     * @return ?FunctionExtension The function extension or null
     */
    public function getExtension(string $name)
        : ?FunctionExtension
    {
        return ($this->extensions[$name] ?? null);
    }
}
