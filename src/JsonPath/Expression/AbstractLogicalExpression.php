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
use JsonScout\Util\RefUtil;


abstract readonly class AbstractLogicalExpression
    implements IFunctionParameter
{
    //==================================================================================================================
    public abstract function evaluate(Node $root, Node $current) : LogicalType;

    //==================================================================================================================
    #[\Override]
    public function toParameter(string $parameterType, Node $root, Node $current)
        : LogicalType
    {
        return $this->evaluate($root, $current);
    }

    #[\Override]
    public function validateParameter(string $parameterType, string &$error): bool
    {
        if ($parameterType !== LogicalType::class)
        {
            $unqualified_name = RefUtil::getUnqualifiedName($parameterType);
            $error = "expected $unqualified_name instead got LogicalType";
            return false;
        }

        return true;
    }
}
