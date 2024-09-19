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

use JsonScout\JsonPath\Object\Node;
use JsonScout\JsonPath\Object\LogicalType;
use JsonScout\JsonPath\Object\NodesType;
use JsonScout\JsonPath\Object\ValueType;
use JsonScout\JsonPath\Parser\ExceptionInternalError;



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
