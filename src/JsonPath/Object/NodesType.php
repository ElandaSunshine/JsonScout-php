<?php

namespace JsonScout\JsonPath\Function\JsonPath\Object;



final readonly class NodesType
{
    //==================================================================================================================
    /** Specifies that nodes should be unpacked in the JSON output. */
    public const FLAG_UNPACK = 1;

    /** Specifies that if there is only one node, it should be inlined. */
    public const FLAG_INLINE = 2;

    /** 
     * Specifies that if {@see JsonScout\NodeList::FLAG_UNPACK} is not set,
     * nodes should be outputted as objects in an array.
     */
    public const FLAG_NODES_AS_OBJECTS = 4;

    //==================================================================================================================
    /** @param array<int,Node> $nodes */
    public function __construct(
        public array $nodes = []
    ) {}

    //==================================================================================================================
    /** 
     * Gets an array with all the nodes unpacked.
     * @return JSONValue[] The value array
     */
    public function toArray()
        : array
    {
        return array_map(function(Node $node) { return $node->value; }, $this->nodes);
    }

    /** 
     * If there is just one node in the node-list, will return this node's value,
     * otherwise {@see JsonScout\NodeList::toArray()} is called.
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
     * If flag {@see JsonScout\NodeList::FLAG_UNPACK} is set, will unpack the nodes and only serialise their values.
     *
     * If flag {@see JsonScout\NodeList::FLAG_INLINE} is set and either {@see JsonScout\NodeList::FLAG_UNPACK} is unset
     * and {@see JsonScout\NodeList::FLAG_NODES_AS_OBJECTS} is set or {@see JsonScout\NodeList::FLAG_UNPACK} is set,
     * will inline the node list as a single element if there was only one node
     *
     * If flag {@see JsonScout\NodeList::FLAG_NODES_AS_OBJECTS} is set and {@see JsonScout\NodeList::FLAG_UNPACK}
     * is not set, will return the node-list as an array of objects with name and value as child elements.
     *
     * Note that if flag {@see JsonScout\NodeList::FLAG_UNPACK} and {@see JsonScout\NodeList::FLAG_NODES_AS_OBJECTS}
     * are not set, duplicate nodes will be removed as there can't be duplicate keys in a json object.
     *
     * @param int $flags       The result flags, specifying what to output
     * @param int $encodeFlags The flags that can be passed to {@see json_encode}
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
