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
 * Declares a single unit of function extensions for JSONPath queries.
 * @link https://elandasunshine.github.io/wiki?page=JsonScout/types/IExtensionProvider%23lang-php
 */
interface IExtensionProvider
{
    /**
     * Creates a list of functions to be registered with this provider.
     * @link https://elandasunshine.github.io/wiki?page=JsonScout/types/IExtensionProvider%23lang-php
     * @return array<non-empty-string,FunctionExtensionCallable> The list of function callbacks to register for this provider
     */
    public function createExtension() : array;
}
