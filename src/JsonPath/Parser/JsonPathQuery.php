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

namespace JsonScout\JsonPath\Parser;

use JsonScout\JsonPath\Expression\QueryExpression;
use JsonScout\JsonPath\Object\Location;
use JsonScout\JsonPath\Object\Node;
use JsonScout\JsonPath\Object\NodesType;



/**
 * Provides an interface to cache and execute a built query.
 * @link https://elandasunshine.github.io/wiki?page=JsonScout/types/JsonPathQuery%23lang-php
 */
final readonly class JsonPathQuery
{
    //==================================================================================================================
    /**
     * Constructs a new JsonPathQuery instance from the given QueryExpression object.
     * 
     * @link https://elandasunshine.github.io/wiki?page=JsonScout/types/JsonPathQuery/JsonPathQuery%23lang-php
     * 
     * @param QueryExpression $expression The query expression
     */
    public function __construct(
        private QueryExpression $expression
    ) {}
    
    //==================================================================================================================
    /**
     * Executes the query on the given JSON data and returns the list of nodes.
     * 
     * @link https://elandasunshine.github.io/wiki?page=JsonScout/types/JsonPathQuery/execute%23lang-php
     * 
     * @param JSONValue $data The parsed JSON data object
     * @return NodesType The resultant nodes list
     */
    public function execute(mixed $data)
        : NodesType
    {
        $root_node = new Node(new Location('$', null), $data);
        return $this->expression->process($root_node, new NodesType([ $root_node ]));
    }
}