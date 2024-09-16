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



final readonly class ComparisonExpression
    extends AbstractLogicalExpression
{
    //==================================================================================================================
    private static function compareEqual(ValueType $left, ValueType $right)
        : bool
    {
        $lvalue = $left ->value;
        $rvalue = $right->value;
        
        if (((is_int($lvalue) || is_float($lvalue)) && (is_int($rvalue) || is_float($rvalue)))
            || (($lvalue instanceof \stdClass) && ($rvalue instanceof \stdClass)))
        {
            return ($lvalue == $rvalue);
        }

        return ($lvalue === $rvalue);
    }

    private static function compareLess(ValueType $left, ValueType $right)
        : bool
    {
        $lvalue = $left ->value;
        $rvalue = $right->value;

        if ((is_int($lvalue) || is_float($lvalue)) && (is_int($rvalue) || is_float($rvalue)))
        {
            return ($lvalue < $rvalue);
        }
        
        if (is_string($lvalue) && is_string($rvalue))
        {
            return (strcmp($lvalue, $rvalue) < 0);
        }

        return false;
    }

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
            ComparisonOperation::Equal       => self::compareEqual($left,  $right),
            ComparisonOperation::NotEqual    => !self::compareEqual($left,  $right),
            ComparisonOperation::LessThan    => self::compareLess($left,  $right),
            ComparisonOperation::GreaterThan => self::compareLess($right, $left),

            ComparisonOperation::LessThanOrEqual
                => (self::compareLess($left, $right) || self::compareEqual($left, $right)),
            ComparisonOperation::GreaterThanOrEqual
                => (self::compareLess($right, $left) || self::compareEqual($left, $right))
        });
    }
}
