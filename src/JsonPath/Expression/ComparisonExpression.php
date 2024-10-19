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
use JsonScout\JsonPath\Object\ValueType;
use JsonScout\Util\RefUtil;
use JsonScout\Util\TypeUtil;

final readonly class ComparisonExpression
    extends AbstractLogicalExpression
{
    //==================================================================================================================
    private static function validateComparable(IComparable $comparable)
        : void
    {
        if (!($comparable instanceof FunctionExpression))
        {
            return;
        }

        if (!$comparable->validForContext(FunctionExtension::CONTEXT_COMPARISON))
        {
            $unqualified_name = RefUtil::getUnqualifiedName($comparable->extension->returnType);
            throw new ExceptionFunctionExtension(
                "function extension '{$comparable->extension->extensionName}' can not be used in a comparison, "
                ."returns '$unqualified_name' but expected ValueType"
            );
        }
    }

    //==================================================================================================================
    public function __construct(
        private ComparisonOperation $type,
        private IComparable         $left,
        private IComparable         $right
    )
    {
        self::validateComparable($left);
        self::validateComparable($right);
    }
    
    //==================================================================================================================
    #[\Override]
    public function evaluate(Node $root, Node $current)
        : LogicalType
    {
        $left  = $this->left ->toComparable($root, $current);
        $right = $this->right->toComparable($root, $current);

        return LogicalType::fromBool(match($this->type) {
            ComparisonOperation::Equal       =>  TypeUtil::equalityCompareOperands($left,  $right),
            ComparisonOperation::NotEqual    => !TypeUtil::equalityCompareOperands($left,  $right),
            ComparisonOperation::LessThan    =>  TypeUtil::lessCompareOperands    ($left,  $right),
            ComparisonOperation::GreaterThan =>  TypeUtil::lessCompareOperands    ($right, $left),

            ComparisonOperation::LessThanOrEqual
                => (TypeUtil::lessCompareOperands($left, $right) || TypeUtil::equalityCompareOperands($left, $right)),
            ComparisonOperation::GreaterThanOrEqual
                => (TypeUtil::lessCompareOperands($right, $left) || TypeUtil::equalityCompareOperands($left, $right))
        });
    }
}
