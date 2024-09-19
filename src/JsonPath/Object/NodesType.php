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

namespace JsonScout\JsonPath\Object;



/**
 * Represents a list of nodes from a query argument.
 * @link https://elandasunshine.github.io/wiki?page=JsonScout/types/NodesType%23lang-php
 */
final readonly class NodesType
{
    //==================================================================================================================
    /** 
     * Specifies that nodes should be unpacked in the JSON output.
     * @link https://elandasunshine.github.io/wiki?page=JsonScout/types/NodesType/toJson%23lang-php
     */
    public const FLAG_UNPACK = 1;

    /** 
     * Specifies that if there is only one node, it should be inlined.
     * @link https://elandasunshine.github.io/wiki?page=JsonScout/types/NodesType/toJson%23lang-php
     */
    public const FLAG_INLINE = 2;

    /** 
     * Specifies that nodes should be outputted as objects in an array.
     * @link https://elandasunshine.github.io/wiki?page=JsonScout/types/NodesType/toJson%23lang-php
     */
    public const FLAG_NODES_AS_OBJECTS = 4;

    //==================================================================================================================
    /** 
     * Constructs a new NodesType object.
     * @link https://elandasunshine.github.io/wiki?page=JsonScout/types/NodesType/NodesType%23lang-php
     * @param array<int,Node> $nodes The node list
     */
    public function __construct(
        public array $nodes = []
    ) {}

    //==================================================================================================================
    /** 
     * Gets an array with all the nodes unpacked.
     * @link https://elandasunshine.github.io/wiki?page=JsonScout/types/NodesType/toArray%23lang-php
     * @return JSONValue[] The value array
     */
    public function toArray()
        : array
    {
        return array_map(function(Node $node) { return $node->value; }, $this->nodes);
    }

    /** 
     * If there is just one node in the node-list will return that node's value, otherwise an array of nodes is called.
     * @link https://elandasunshine.github.io/wiki?page=JsonScout/types/NodesType/toValue%23lang-php
     * @return JSONValue The unpacked value
     */
    public function toValue()
        : mixed
    {
        return (count($this->nodes) === 1 ? $this->nodes[0]->value : $this->toArray());
    }

    /**
     * Returns the contents of this node-list as JSON string.
     *
     * @link https://elandasunshine.github.io/wiki?page=JsonScout/types/NodesType/toJson%23lang-php
     * 
     * @param int $flags       The result flags, specifying how to output the nodes
     * @param int $encodeFlags Any flags that can be passed to {@see json_encode}
     * 
     * @return string|false The JSON string or false if there was a problem encoding the nodes
     */
    public function toJson(int $flags = 0, int $encodeFlags = 0)
        : string|false
    {
        $contents = [];
        $is_assoc = false;

        if (($flags & self::FLAG_UNPACK) == self::FLAG_UNPACK)
        {
            $contents = $this->toArray();
        }
        else
        {
            if (($flags & self::FLAG_NODES_AS_OBJECTS) == self::FLAG_NODES_AS_OBJECTS)
            {
                foreach ($this->nodes as $node)
                {
                    $contents[] = [
                        'location' => (string) $node->location,
                        'value'    => $node->value
                    ];
                }
            }
            else
            {
                $is_assoc = true;

                foreach ($this->nodes as $node)
                {
                    $contents[(string) $node->location] = $node->value;
                }
            }
        }

        if (($flags & self::FLAG_INLINE) == self::FLAG_INLINE
            && count($contents) === 1
            && !$is_assoc)
        {
            $contents = $contents[0];
        }

        return json_encode($contents, $encodeFlags);
    }
}
