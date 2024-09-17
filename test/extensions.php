<?php
declare(strict_types=1);

require_once '../vendor/autoload.php';

use JsonScout\JsonScout;


// Example from: https://json.org/example.html
$examples = [
    "input" => (object) [
        "glossary" => (object) [
            "title" => "example glossary",
            "GlossDiv" => (object) [
                "title" => "S",
                "GlossList" => (object) [
                    "GlossEntry" => (object) [
                        "ID" => "SGML",
                        "SortAs" => "SGML",
                        "GlossTerm" => "Standard Generalized Markup Language",
                        "Acronym" => "SGML",
                        "Abbrev" => "ISO 8879:1986",
                        "GlossDef" => (object) [
                            "para" => "A meta-markup language, used to create markup languages such as DocBook.",
                            "GlossSeeAlso" => ["GML", "XML"]
                        ],
                        "GlossSee" => "markup"
                    ]
                ]
            ]
        ]
    ]
];

$test_cases = [
    // contains
    '$.store.book[*].author' => [
        'data'   => 'input',
        'expect' => [

        ]
    ],
];

(new TestBase($test_cases, $examples))->runTests();
