<?php

namespace JsonScout\JsonPath\Function\JsonPath\Expression;

use JsonScout\JsonPath\Function\JsonPath\Object\Node;
use JsonScout\JsonPath\Function\JsonPath\Object\LogicalType;
use JsonScout\JsonPath\Function\JsonPath\Object\ValueType;



final readonly class ComparisonExpression
    extends AbstractLogicalExpression
{
    //==================================================================================================================
    private static function compareEqual(ValueType $left, ValueType $right)
        : bool
    {
        if (!$left->hasValue() || !$right->hasValue())
        {
            return ($left === $right);
        }

        $lvalue = $left ->value;
        $rvalue = $right->value;

        if (($lvalue instanceof \stdClass) || ($rvalue instanceof \stdClass))
        {
            return ($lvalue == $rvalue);
        }
        
        if ((is_int($lvalue) || is_float($lvalue)) && (is_int($rvalue) || is_float($rvalue)))
        {
            return ($lvalue == $rvalue);
        }

        return ($lvalue === $rvalue);
    }

    private static function compareLess(ValueType $left, ValueType $right)
        : bool
    {
        if (!$left->hasValue() || !$right->hasValue())
        {
            return false;
        }

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
    public function __construct(
        private ComparisonOperation $type,
        private IComparable         $left,
        private IComparable         $right
    ) {}
    
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
