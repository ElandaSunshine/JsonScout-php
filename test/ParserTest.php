<?php
declare(strict_types=1);

require_once __DIR__ . '/TestBase.php';



//======================================================================================================================
class ParserTest
    extends TestBase
{
    #[\Override]
    public function setUp()
        : void
    {
        $this->testData = [
            'example1' => (object) [
                "store" => (object) [
                    "book" => [
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
                    "bicycle" => (object) [
                        "color" => "red",
                        "price" => 399
                    ]
                ]
            ],
            'example2' => (object) [
                "o" => (object) [
                    "j j" => (object) [
                        "k.k" => 3
                    ]
                ],
                "'" => (object) [
                    "@" => 2
                ]
            ],
            'example3' => (object) [
                "o" => (object)[
                    "j" => 1,
                    "k" => 2
                ],
                "a" => [ 5, 3 ]
            ],
            'example4' => (object) [
                "a" => [
                    3, 5, 1, 2, 4, 6,
                    (object) [ "b" => "j" ],
                    (object) [ "b" => "k" ],
                    (object) [ "b" => (object)[] ],
                    (object) [ "b" => "kilo" ]
                ],
                "o" => (object) [
                    "p" => 1,
                    "q" => 2,
                    "r" => 3,
                    "s" => 5,
                    "t" => (object) [ "u" => 6 ]
                ],
                "e" => "f"
            ],
            'example5' => (object) [
                "o" => (object) [
                    "j" => 1,
                    "k" => 2
                ],
                "a" => [
                    5, 3,
                    [
                        (object) [ "j" => 4 ],
                        (object) [ "k" => 6 ]
                    ]
                ]
            ]
        ];
        
        $this->testCases = [
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
                'expect' => [ 8.95, 12.99, 8.99, 22.99, 399 ],
                'order'  => false
            ],
            '$..book[2]' => [
                'data'   => 'example1',
                'expect' => [
                    (object) [
                        "category" => "fiction",
                        "author" => "Herman Melville",
                        "title" => "Moby Dick",
                        "isbn" => "0-553-21311-3",
                        "price" => 8.99
                    ]
                ]
            ],
            '$..book[2].author' => [
                'data'   => 'example1',
                'expect' => [ "Herman Melville" ]
            ],
            '$..book[2].publisher' => [
                'data'   => 'example1',
                'expect' => []
            ],
            '$..book[-1]' => [
                'data'   => 'example1',
                'expect' => [
                    (object) [
                        "category" => "fiction",
                        "author" => "J. R. R. Tolkien",
                        "title" => "The Lord of the Rings",
                        "isbn" => "0-395-19395-8",
                        "price" => 22.99
                    ]
                ]
            ],
            '$..book[0,1]' => [
                'data'   => 'example1',
                'expect' => [
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
                    ]
                ]
            ],
            '$..book[:2]' => [
                'data'   => 'example1',
                'expect' => [
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
                    ]
                ]
            ],
            '$..book[?@.isbn]' => [
                'data'   => 'example1',
                'expect' => [
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
                ]
            ],
            '$..book[?@.price<10]' => [
                'data'   => 'example1',
                'expect' => [
                    (object) [
                        "category" => "reference",
                        "author" => "Nigel Rees",
                        "title" => "Sayings of the Century",
                        "price" => 8.95
                    ],
                    (object) [
                        "category" => "fiction",
                        "author" => "Herman Melville",
                        "title" => "Moby Dick",
                        "isbn" => "0-553-21311-3",
                        "price" => 8.99
                    ],
                ]
            ],
            '$..*' => [
                'data'   => 'example1',
                'expect' => [
                    (object) [
                        "book" => [
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
                        "bicycle" => (object) [
                            "color" => "red",
                            "price" => 399
                        ]
                    ],
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
                    ],
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
                    ],
                    "red",
                    399,
                    "reference",
                    "Nigel Rees",
                    "Sayings of the Century",
                    8.95,
                    "fiction",
                    "Evelyn Waugh",
                    "Sword of Honour",
                    12.99,
                    "fiction",
                    "Herman Melville",
                    "Moby Dick",
                    "0-553-21311-3",
                    8.99,
                    "fiction",
                    "J. R. R. Tolkien",
                    "The Lord of the Rings",
                    "0-395-19395-8",
                    22.99
                ]
            ],
            
            // Root op
            '$' => [
                'data'   => (object) [ "k" => "v" ],
                'expect' => [
                    (object) [ "k" => "v" ]
                ]
            ],
            
            // Child selector
            '$.o["j j"]' => [
                'data'   => 'example2',
                'expect' => [
                    (object) [ "k.k" => 3 ]
                ]
            ],
            '$.o["j j"]["k.k"]' => [
                'data'   => 'example2',
                'expect' => [ 3 ]
            ],
            '$["\'"]["@"]' => [
                'data'   => 'example2',
                'expect' => [ 2 ]
            ],
            
            // Wildcard selector
            '$[*]' => [
                'data'   => 'example3',
                'expect' => [
                    (object)[ "j" => 1, "k" => 2 ],
                    [ 5, 3 ]
                ]
            ],
            '$.o[*]' => [
                'data'   => 'example3',
                'expect' => [ 1, 2 ]
            ],
            '$.o[*, *]' => [
                'data'   => 'example3',
                'expect' => [ 1, 2, 1, 2 ]
            ],
            '$.a[*]' => [
                'data'   => 'example3',
                'expect' => [ 5, 3 ]
            ],
            
            // index selector
            '$[1]' => [
                'data'   => [ "a", "b" ],
                'expect' => [ "b" ]
            ],
            '$[-2]' => [
                'data'   => [ "a", "b" ],
                'expect' => [ "a" ]
            ],
            
            // slice selector
            '$[1:3]' => [
                'data'   => [ "a", "b", "c", "d", "e", "f", "g" ],
                'expect' => [ "b", "c" ]
            ],
            '$[5:]' => [
                'data'   => [ "a", "b", "c", "d", "e", "f", "g" ],
                'expect' => [ "f", "g" ]
            ],
            '$[1:5:2]' => [
                'data'   => [ "a", "b", "c", "d", "e", "f", "g" ],
                'expect' => [ "b", "d" ]
            ],
            '$[5:1:-2]' => [
                'data'   => [ "a", "b", "c", "d", "e", "f", "g" ],
                'expect' => [ "f", "d" ]
            ],
            '$[::-1]' => [
                'data'   => [ "a", "b", "c", "d", "e", "f", "g" ],
                'expect' => [ "g", "f", "e", "d", "c", "b", "a" ]
            ],
        
            // filter selector
            '$.a[?@.b == "kilo"]' => [
                'data'   => 'example4',
                'expect' => [ (object)[ "b" => "kilo" ] ]
            ],
            '$.a[?(@.b == "kilo")]' => [
                'data'   => 'example4',
                'expect' => [ (object)[ "b" => "kilo" ] ]
            ],
            '$.a[?@ > 3.5]' => [
                'data'   => 'example4',
                'expect' => [ 5, 4, 6 ]
            ],
            '$.a[?@.b]' => [
                'data'   => 'example4',
                'expect' => [
                    (object) [ "b" => "j" ],
                    (object) [ "b" => "k" ],
                    (object) [ "b" => (object)[] ],
                    (object) [ "b" => "kilo" ]
                ]
            ],
            '$[?@.*]' => [
                'data'   => 'example4',
                'expect' => [
                    [ 
                        3, 5, 1, 2, 4, 6,
                        (object) [ "b" => "j" ],
                        (object) [ "b" => "k" ],
                        (object) [ "b" => (object)[] ],
                        (object) [ "b" => "kilo" ]
                    ],
                    (object) [
                        "p" => 1,
                        "q" => 2,
                        "r" => 3,
                        "s" => 5,
                        "t" => (object)[ "u" => 6 ]
                    ]
                ]
            ],
            '$[?@[?@.b]]' => [
                'data'   => 'example4',
                'expect' => [
                    [ 
                        3, 5, 1, 2, 4, 6,
                        (object) [ "b" => "j" ],
                        (object) [ "b" => "k" ],
                        (object) [ "b" => (object)[] ],
                        (object) [ "b" => "kilo" ]
                    ]
                ]
            ],
            '$.o[?@<3, ?@<3]' => [
                'data'   => 'example4',
                'expect' => [ 1, 2, 2, 1 ],
                'order'  => false
            ],
            '$.a[?@<2 || @.b == "k"]' => [
                'data'   => 'example4',
                'expect' => [
                    1,
                    (object) [ "b" => "k" ]
                ]
            ],
            '$.a[?match(@.b, "[jk]")]' => [
                'data'   => 'example4',
                'expect' => [
                    (object) [ "b" => "j" ],
                    (object) [ "b" => "k" ]
                ]
            ],
            '$.a[?search(@.b, "[jk]")]' => [
                'data'   => 'example4',
                'expect' => [
                    (object) [ "b" => "j" ],
                    (object) [ "b" => "k" ],
                    (object) [ "b" => "kilo" ]
                ]
            ],
            '$.o[?@ > 1 && @ < 4]' => [
                'data'   => 'example4',
                'expect' => [ 2, 3 ],
                'order'  => false
            ],
            '$.o[?@.u || @.x]' => [
                'data'   => 'example4',
                'expect' => [ (object) [ "u" => 6 ] ]
            ],
            '$.a[?@.b == $.x]' => [
                'data'   => 'example4',
                'expect' => [ 3, 5, 1, 2, 4, 6 ]
            ],
            '$.a[?@ == @]' => [
                'data'   => 'example4',
                'expect' => [ 
                    3, 5, 1, 2, 4, 6,
                    (object) [ "b" => "j" ],
                    (object) [ "b" => "k" ],
                    (object) [ "b" => (object)[] ],
                    (object) [ "b" => "kilo" ]
                ]
            ],
        
            // segments
            '$[0, 3]' => [
                'data'   => ["a", "b", "c", "d", "e", "f", "g"],
                'expect' => [ "a", "d" ]
            ],
            '$[0:2, 5]' => [
                'data'   => ["a", "b", "c", "d", "e", "f", "g"],
                'expect' => [ "a", "b", "f" ]
            ],
            '$[0, 0]' => [
                'data'   => ["a", "b", "c", "d", "e", "f", "g"],
                'expect' => [ "a", "a" ]
            ],
        
            // descendant segment
            '$..j' => [
                'data'   => 'example5',
                'expect' => [ 1, 4 ],
                'order'  => false
            ],
            '$..[0]' => [
                'data'   => 'example5',
                'expect' => [
                    5,
                    (object) [ "j" => 4 ]
                ]
            ],
            '$..[*]' => [
                'data'   => 'example5',
                'expect' => [
                    (object) [ "j" => 1, "k" => 2 ],
                    [ 
                        5, 3,
                        [
                            (object) [ "j" => 4 ],
                            (object) [ "k" => 6 ]
                        ]
                    ],
                    1, 2, 5, 3,
                    [
                        (object) [ "j" => 4 ],
                        (object) [ "k" => 6 ]
                    ],
                    (object) [ "j" => 4 ],
                    (object) [ "k" => 6 ],
                    4, 6
                ]
            ],
            '$..*' => [
                'data'   => 'example5',
                'expect' => [
                    (object) [ "j" => 1, "k" => 2 ],
                    [ 
                        5, 3,
                        [
                            (object) [ "j" => 4 ],
                            (object) [ "k" => 6 ]
                        ]
                    ],
                    1, 2, 5, 3,
                    [
                        (object) [ "j" => 4 ],
                        (object) [ "k" => 6 ]
                    ],
                    (object) [ "j" => 4 ],
                    (object) [ "k" => 6 ],
                    4, 6
                ]
            ],
            '$..o' => [
                'data'   => 'example5',
                'expect' => [
                    (object) [ "j" => 1, "k" => 2 ]
                ]
            ],
            '$.o..[*, *]' => [
                'data'   => 'example5',
                'expect' => [ 1, 2, 2, 1 ],
                'order'  => false
            ],
            '$.a..[0, 1]' => [
                'data'   => 'example5',
                'expect' => [
                    5, 3,
                    (object) [ "j" => 4 ],
                    (object) [ "k" => 6 ]
                ],
                'order' => false
            ],
        
            // null semantics
            '$.a' => [
                'data' => (object) [
                    "a"    => null,
                    "b"    => [ null ],
                    "c"    => [ (object)[] ],
                    "null" => 1
                ],
                'expect' => [ null ]
            ],
            '$.a[0]' => [
                'data' => (object) [
                    "a"    => null,
                    "b"    => [ null ],
                    "c"    => [ (object)[] ],
                    "null" => 1
                ],
                'expect' => [ ]
            ],
            '$.a.d' => [
                'data' => (object) [
                    "a"    => null,
                    "b"    => [ null ],
                    "c"    => [ (object)[] ],
                    "null" => 1
                ],
                'expect' => [ ]
            ],
            '$.b[0]' => [
                'data' => (object) [
                    "a"    => null,
                    "b"    => [ null ],
                    "c"    => [ (object)[] ],
                    "null" => 1
                ],
                'expect' => [ null ]
            ],
            '$.b[*]' => [
                'data' => (object) [
                    "a"    => null,
                    "b"    => [ null ],
                    "c"    => [ (object)[] ],
                    "null" => 1
                ],
                'expect' => [ null ]
            ],
            '$.b[?@]' => [
                'data' => (object) [
                    "a"    => null,
                    "b"    => [ null ],
                    "c"    => [ (object)[] ],
                    "null" => 1
                ],
                'expect' => [ null ]
            ],
            '$.b[?@ == null]' => [
                'data' => (object) [
                    "a"    => null,
                    "b"    => [ null ],
                    "c"    => [ (object)[] ],
                    "null" => 1
                ],
                'expect' => [ null ]
            ],
            '$.c[?@.d == null]' => [
                'data' => (object) [
                    "a"    => null,
                    "b"    => [ null ],
                    "c"    => [ (object)[] ],
                    "null" => 1
                ],
                'expect' => [ ]
            ],
            '$.null' => [
                'data' => (object) [
                    "a"    => null,
                    "b"    => [ null ],
                    "c"    => [ (object)[] ],
                    "null" => 1
                ],
                'expect' => [ 1 ]
            ]
        ];
    }
}
