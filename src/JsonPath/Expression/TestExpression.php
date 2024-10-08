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
use JsonScout\JsonPath\Object\Node;
use JsonScout\JsonPath\Object\LogicalType;
use JsonScout\Util\RefUtil;



final readonly class TestExpression
    extends AbstractLogicalExpression
{
    //==================================================================================================================
    public function __construct(
        private ITestable $testExpr,
        private bool      $negate
    )
    {
        if (($testExpr instanceof FunctionExpression) && !$testExpr->validForContext(FunctionExtension::CONTEXT_TEST))
        {
            $unqualified_name = RefUtil::getUnqualifiedName($testExpr->extension->returnType);
            throw new ExceptionFunctionExtension(
                "function extension '{$testExpr->extension->extensionName}' can not be used in a test expression, "
                ."returns '$unqualified_name' but expected LogicalType (or NodesType)"
            );
        }
    }
    
    //==================================================================================================================
    #[\Override]
    public function evaluate(Node $root, Node $current)
        : LogicalType
    {
        $eval = $this->testExpr->test($root, $current);
        return ($this->negate ? $eval->negate() : $eval);
    }
}
