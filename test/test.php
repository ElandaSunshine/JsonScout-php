<?php

require_once '../vendor/autoload.php';

use JsonScout\JsonScout;
use JsonScout\JsonPath\Object\NodesType;



$example1 = '
{ 
    "store": {
        "book": [
            { 
                "category": "reference",
                "author": "Nigel Rees",
                "title": "Sayings of the Century",
                "price": 8.95
            },
            { 
                "category": "fiction",
                "author": "Evelyn Waugh",
                "title": "Sword of Honour",
                "price": 12.99
            },
            { 
                "category": "fiction",
                "author": "Herman Melville",
                "title": "Moby Dick",
                "isbn": "0-553-21311-3",
                "price": 8.99
            },
            { 
                "category": "fiction",
                "author": "J. R. R. Tolkien",
                "title": "The Lord of the Rings",
                "isbn": "0-395-19395-8",
                "price": 22.99
            }
        ],
        "bicycle": {
            "color": "red",
            "price": 399
        }
    }
}
';

$examples = [
    'example1' => JsonScout::fromString($example1)
];

$query_table = [
    '$.store.book[*].author' => [
        'data'   => 'example1',
        'expect' => [ 'Nigel Rees', 'Evelyn Waugh', 'Herman Melville', 'J. R. R. Tolkien' ]
    ],
    '$..author' => [
        'data'   => 'example1',
        'expect' => [ 'Nigel Rees', 'Evelyn Waugh', 'Herman Melville', 'J. R. R. Tolkien' ]
    ],
    '$.store.*' => [
        'data'   => 'example1',
        'expect' => [
            [
                (object) [ 
                    "category" => "reference",
                    "author" => "Nigel Rees",
                    "title" => "Sayings of the Century",
                    "price" => 8.95
                ],
                (object) [  
                    "category" => "fiction",
                    "author" => "Evelyn Waugh",
                    "title" => "Sword of Honour",
                    "price" => 12.99
                ],
                (object) [ 
                    "category" => "fiction",
                    "author" => "Herman Melville",
                    "title" => "Moby Dick",
                    "isbn" => "0-553-21311-3",
                    "price" => 8.99
                ],
                (object) [  
                    "category" => "fiction",
                    "author" => "J. R. R. Tolkien",
                    "title" => "The Lord of the Rings",
                    "isbn" => "0-395-19395-8",
                    "price" => 22.99
                ]
            ],
            (object) [
                "color" => "red",
                "price" => 399
            ]
        ]
    ],
    '$.store..price' => [
        'data'   => 'example1',
        'expect' => [ 8.95, 12.99, 8.99, 22.99, 399 ]
    ]
];

foreach ($query_table as $query => $data)
{
    $result = JsonScout::query($query, $examples[$data['data']])->toArray();

    if (!diffTest($result, $data['expect']))
    {
        throw new RuntimeException("query '$query' failed with output: ".json_encode($result));
    }
}

echo 'Successfully approved all queries!';

function diffTest(mixed $input, mixed $expected)
    : bool
{
    if (!is_array($input))
    {
        if ($input instanceof \stdClass)
        {
            return ($input == $expected);
        }

        return ($input === $expected);
    }

    if (!is_array($expected))
    {
        return false;
    }

    foreach ($input as $key => $element)
    {
        if (!array_key_exists($key, $expected))
        {
            return false;
        }

        if (is_array($element))
        {
            if (!diffTest($element, $expected[$key]))
            {
                return false;
            }
        }
        else if ($element instanceof \stdClass)
        {
            if ($element != $expected[$key])
            {
                return false;
            }
        }
        else if ($element !== $expected[$key])
        {
            return false;
        }
    }

    return true;
}
