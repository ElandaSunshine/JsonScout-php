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

use JsonScout\JsonPath\Function\Builtins\ArrayExtension;
use JsonScout\JsonPath\Function\Builtins\CommonExtension;
use JsonScout\JsonPath\Function\Builtins\MathExtension;
use JsonScout\JsonPath\Function\Builtins\StandardExtension;
use JsonScout\JsonPath\Function\Builtins\StringExtension;
use JsonScout\JsonPath\Function\Builtins\TypeExtension;

/**
 * Manages a global list of function extensions for JSONPath queries.
 * @link https://elandasunshine.github.io/wiki?page=JsonScout/types/FunctionRegistry%23lang-php
 */
final class FunctionRegistry
{
    //==================================================================================================================
    private static ?self $instance = null;

    //==================================================================================================================
    /**
     * Gets the global function extension registry instance.
     * @link https://elandasunshine.github.io/wiki?page=JsonScout/types/FunctionRegistry/getInstance%23lang-php
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
     * @var array<non-empty-string,FunctionExtension> $extensions
     */
    private array $extensions = [];

    //==================================================================================================================
    private function __construct()
    {
        $this->registerExtensionProvider(
            // Standard extensions
            StandardExtension::class,

            // Additional extensions
            ArrayExtension::class, StringExtension::class, MathExtension::class, TypeExtension::class,
            CommonExtension::class
        );
    }

    //==================================================================================================================
    /**
     * Gets a function extension by name (with namespace prefix) or null if no such function extension exists.
     * 
     * @link https://elandasunshine.github.io/wiki?page=JsonScout/types/FunctionRegistry/getExtension%23lang-php
     * 
     * @param string $name The name of the function extension
     * @return ?FunctionExtension The function extension or null
     */
    public function getExtension(string $name) : ?FunctionExtension { return ($this->extensions[$name] ?? null); }
    
    //------------------------------------------------------------------------------------------------------------------
    /**
     * Tries to register one or more function extension provider.
     *
     * @link https://elandasunshine.github.io/wiki?page=JsonScout/types/FunctionRegistry/registerExtensionProvider%23lang-php
     * 
     * @param class-string<IExtensionProvider> $class     The class providing the extension functions
     * @param class-string<IExtensionProvider> ...$other  Additional classes to register for this namespace
     *
     * @throws ExceptionFunctionRegistration Thrown if there was an issue registering this function extension
     */
    public function registerExtensionProvider(string $class, string ...$other)
        : void
    {
        $classes = [ $class, ...$other ];

        try
        {
            foreach ($classes as $class_name)
            {
                $interfaces = class_implements($class_name);
                
                if (!in_array(IExtensionProvider::class, $interfaces, true))
                {
                    throw new ExceptionFunctionRegistration("class '$class_name' is not an IFunctionProvider");
                }
                
                $instance  = new $class_name();
                $functions = $instance->createExtension();
                
                foreach ($functions as $name => $function)
                {
                    /** @phpstan-ignore function.alreadyNarrowedType */
                    if (!is_callable($function))
                    {
                        throw new ExceptionFunctionRegistration("'$function' is not a callable");
                    }
                    
                    $this->registerExtension($name, $function);
                }
            }
        }
        catch (\Exception $ex)
        {
            throw new ExceptionFunctionRegistration($ex->getMessage());
        }
    }

    /**
     * Tries to register a callable as function extension.
     * 
     * @link https://elandasunshine.github.io/wiki?page=JsonScout/types/FunctionRegistry/registerExtension%23lang-php
     * 
     * @param non-empty-string  $name      The name of the function extension
     * @param callable          $invocable The callable to register
     * 
     * @param-later-invoked-callable $invocable
     */
    public function registerExtension(string $name, callable $invocable)
        : void
    {
        if (array_key_exists($name, $this->extensions))
        {
            throw new ExceptionFunctionRegistration(
                "function extension with the name '$name' already registered"
            );
        }

        $this->extensions[$name] = new FunctionExtension($name, $invocable);
    }
}
