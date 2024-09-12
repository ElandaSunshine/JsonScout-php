<?php

namespace JsonScout\JsonPath\Function\JsonPath\Expression;

use JsonScout\JsonPath\Function\JsonPath\Object\Node;
use JsonScout\JsonPath\Function\JsonPath\Object\LogicalType;
use JsonScout\JsonPath\Function\JsonPath\Object\NodesType;
use JsonScout\JsonPath\Function\JsonPath\Object\ValueType;
use JsonScout\JsonPath\Function\JsonPath\Parser\ExceptionInternalError;



final readonly class LogicalExpression
    extends AbstractLogicalExpression
{
    //==================================================================================================================
    /** @param AbstractLogicalExpression[] $subExps */
    public function __construct(
        private LogicalOperation $type,
        private array            $subExps
    ) {}
    
    //==================================================================================================================
    #[\Override]
    public function evaluate(Node $root, Node $current)
        : LogicalType
    {
        switch ($this->type)
        {
            case LogicalOperation::Or:
            {
                $result = LogicalType::False;

                foreach ($this->subExps as $expr)
                {
                    if ($expr->evaluate($root, $current) === LogicalType::True)
                    {
                        $result = LogicalType::True;
                        break;
                    }
                }

                break;
            }

            case LogicalOperation::And:
            {
                $result = LogicalType::True;

                foreach ($this->subExps as $expr)
                {
                    if ($expr->evaluate($root, $current) === LogicalType::False)
                    {
                        $result = LogicalType::False;
                        break;
                    }
                }

                break;
            }

            default: throw new ExceptionInternalError("unhandled logical operation '{$this->type->name}'");
        }

        return $result;
    }
}
