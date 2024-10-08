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

namespace JsonScout;

use JsonScout\JsonPath\Function\ExceptionFunctionExtension;
use JsonScout\JsonPath\Object\NodesType;
use JsonScout\JsonPath\Parser\JsonPathQuery;
use JsonScout\JsonPath\Parser\JsonPathVisitor;
use JsonScout\JsonPath\Parser\JsonPathLexer;
use JsonScout\JsonPath\Parser\JsonPathParser;
use JsonScout\JsonPath\Parser\ExceptionInternalError;
use JsonScout\JsonPath\Parser\ExceptionSyntaxError;

use Antlr\Antlr4\Runtime\Atn\ParserATNSimulator;
use Antlr\Antlr4\Runtime\Atn\PredictionMode;
use Antlr\Antlr4\Runtime\CommonTokenStream;
use Antlr\Antlr4\Runtime\InputStream;
use JsonScout\JsonPath\Parser\JsonPathErrorStrategy;
use JsonScout\JsonPath\Parser\SyntaxErrorListener;



/**
 * The main class of JsonScout, listing the entry points for the various features this library provides.
 * @link https://elandasunshine.github.io/wiki?page=JsonScout/types/JsonScout%23lang-php
 */
final class JsonScout
{
    //==================================================================================================================
    /**
     * Parses the given input string, tries to parse it as JSON and returns the JSON value.
     * 
     * @link https://elandasunshine.github.io/wiki?page=JsonScout/types/JsonScout/fromString%23lang-php
     * 
     * @param string $input The input JSON string
     * @return JSONValue A new JsonScout instance with the parsed JSON data
     */
    public static function fromString(string $input)
        : mixed
    {
        /** @var JSONValue */
        return json_decode($input);
    }

    /**
     * Reads the given file, tries to parse the entire file as JSON string and returns the JSON value.
     * 
     * @link https://elandasunshine.github.io/wiki?page=JsonScout/types/JsonScout/fromFile%23lang-php
     * 
     * @param string $file The file or URL
     * @return JSONValue A new JsonScout instance with the parsed JSON data
     */
    public static function fromFile(string $file)
        : mixed
    {
        $input = file_get_contents($file);

        if ($input === false)
        {
            throw new ExceptionReadFailure("error reading file '$file'");
        }

        /** @var JSONValue */
        return json_decode($input);
    }

    /**
     * Reads the given stream, tries to parse the entire stream as JSON string and returns the JSON value.
     * 
     * @link https://elandasunshine.github.io/wiki?page=JsonScout/types/JsonScout/fromStream%23lang-php
     * 
     * @param resource $stream The stream resource
     * @param bool $fromStart Should the stream be rewound back to the beginning
     * 
     * @return JSONValue A new JsonScout instance with the parsed JSON data
     */
    public static function fromStream($stream, bool $fromStart = true)
        : mixed
    {
        $input = stream_get_contents($stream, null, ($fromStart ? 0 : -1));

        if ($input === false)
        {
            throw new ExceptionReadFailure('error reading from stream');
        }

        /** @var JSONValue */
        return json_decode($input);
    }
    
    //==================================================================================================================
    /**
     * Compiles the given JSONPath query for a reusable AST with different query arguments.
     * 
     * @link https://elandasunshine.github.io/wiki?page=JsonScout/types/JsonScout/compile%23lang-php
     * 
     * @param non-empty-string $query The JSONPath query string to evaluate
     * @return JsonPathQuery The compiled query object
     * 
     * @throws ExceptionSyntaxError Thrown whenever the query has a syntax problem 
     * @throws ExceptionInternalError
     * Thrown whenever there was an internal issue with the library (because of the developer's stupidity)
     * @throws ExceptionFunctionExtension Thrown whenever there was an issue involving a function extension
     */
    public static function compile(string $query)
        : JsonPathQuery
    {
        $input  = InputStream::fromString($query);
        $lexer  = new JsonPathLexer($input);
        $tokens = new CommonTokenStream($lexer);
        $parser = new JsonPathParser($tokens);

        $interpreter = $parser->getInterpreter();
        assert($interpreter instanceof ParserATNSimulator);

        $interpreter->setPredictionMode(PredictionMode::SLL);
        $parser->setErrorHandler(new JsonPathErrorStrategy()); // Controls any errors we get during parsing
        $parser->addErrorListener(new SyntaxErrorListener()); // Controls errors that were missed by our strategy

        $visitor = new JsonPathVisitor();
        $tree    = $parser->query();

        $result = $visitor->visit($tree);
        assert($result instanceof JsonPathQuery);

        return $result;
    }

    //==================================================================================================================
    /**
     * Applies the given JSONPath query to the given query argument and returns a {@see NodesType}
     * with all nodes that matched the query.
     * 
     * @link https://elandasunshine.github.io/wiki?page=JsonScout/types/JsonScout/query%23lang-php
     * 
     * @param non-empty-string $query The JSONPath query string to evaluate
     * @param JSONValue        $data  The query argument to query
     * 
     * @return NodesType The resultant node list
     * 
     * @throws ExceptionSyntaxError Thrown whenever the query has a syntax problem
     * @throws ExceptionInternalError
     * Thrown whenever there was an internal issue with the library (because of the developer's stupidity)
     * @throws ExceptionFunctionExtension Thrown whenever there was an issue involving a function extension
     */
    public static function query(string $query, mixed $data)
        : NodesType
    {
        $query_expression = self::compile($query);
        return $query_expression->execute($data);
    }
}
