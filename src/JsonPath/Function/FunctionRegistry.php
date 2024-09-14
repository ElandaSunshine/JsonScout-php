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
use JsonScout\JsonPath\Function\Builtins\StandardExtension;
use JsonScout\JsonPath\Function\Builtins\StringExtension;



/**
 * @phpstan-import-type FunctionExtensionCallable from FunctionExtension
 * 
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

    /**
     * @var string[] $namespaces
     */
    private array $namespaces = [];

    //==================================================================================================================
    private function __construct()
    {
        // StandardExtension extensions
        $this->registerExtension('', 'length', [ StandardExtension::class, 'length' ]);
        $this->registerExtension('', 'count',  [ StandardExtension::class, 'count'  ]);
        $this->registerExtension('', 'match',  [ StandardExtension::class, 'match'  ]);
        $this->registerExtension('', 'search', [ StandardExtension::class, 'search' ]);
        $this->registerExtension('', 'value',  [ StandardExtension::class, 'value'  ]);

        $this->registerUserExtension('array', ArrayExtension ::class);
        $this->registerUserExtension('str',   StringExtension::class);
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
    public function getExtension(string $name)
        : ?FunctionExtension
    {
        return ($this->extensions[$name] ?? null);
    }

    //------------------------------------------------------------------------------------------------------------------
    /**
     * Tries to register a namespace with custom function extensions.
     *
     * @link https://elandasunshine.github.io/wiki?page=JsonScout/types/FunctionRegistry/registerUserExtension%23lang-php
     * 
     * @param string       $namespaceName The name of the extension namespace
     * @param class-string $class         The class providing the extension functions
     * @param class-string ...$other      Additional classes to register for this namespace
     *
     * @throws ExceptionFunctionRegistration Thrown if there was an issue registering this function extension
     */
    public function registerUserExtension(string $namespaceName, string $class, string ...$other)
        : void
    {
        if (preg_match('/[a-z][0-9a-z]{2,}/', $namespaceName) === false)
        {
            throw new ExceptionFunctionRegistration(
                "invalid extension namespace '$namespaceName', must start with a lowercase letter, "
                ."must be at least 3 characters long and can only contain lowercase letters and numbers"
            );
        }

        if (in_array($namespaceName, $this->namespaces, true))
        {
            throw new ExceptionFunctionRegistration("extension namespace '$namespaceName' already registered");
        }

        try
        {
            /** @var class-string $class_name */
            foreach ([ $class, ...$other ] as $class_name)
            {
                $refl = new \ReflectionClass($class_name);

                foreach ($refl->getMethods(\ReflectionMethod::IS_STATIC | \ReflectionMethod::IS_PUBLIC) as $method)
                {
                    $func_attributes = $method->getAttributes(ExtensionFunction::class);

                    if (count($func_attributes) === 0)
                    {
                        continue;
                    }

                    /** @var ExtensionFunction $extension_function */
                    $extension_function = $func_attributes[0]->newInstance();
                    $func_name          = ($extension_function->name ?? $method->getName());

                    $this->registerExtension($namespaceName, $func_name, [ $class_name, $method->getShortName() ]);
                }

                $this->namespaces[] = $namespaceName;
            }
        }
        catch (\Exception $ex)
        {
            throw new ExceptionFunctionRegistration($ex->getMessage());
        }
    }

    //==================================================================================================================
    /**
     * @param non-empty-string          $name
     * @param FunctionExtensionCallable $functionExtension
     */
    private function registerExtension(string $namespace, string $name, callable $functionExtension)
        : void
    {
        $full_name = $namespace . '_' . $name;

        if (array_key_exists($full_name, $this->extensions))
        {
            throw new ExceptionFunctionRegistration(
                "function extension with the name '$name' already registered for namespace '$namespace'"
            );
        }

        $this->extensions[$full_name] = new FunctionExtension($namespace, $name, $functionExtension);
    }
}
