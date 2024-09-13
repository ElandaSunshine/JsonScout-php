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

namespace JsonScout\JsonPath\Parser {
    use Antlr\Antlr4\Runtime\Atn\ATN;
    use Antlr\Antlr4\Runtime\Atn\ATNDeserializer;
    use Antlr\Antlr4\Runtime\Atn\ParserATNSimulator;
    use Antlr\Antlr4\Runtime\Dfa\DFA;
    use Antlr\Antlr4\Runtime\Error\Exceptions\FailedPredicateException;
    use Antlr\Antlr4\Runtime\Error\Exceptions\NoViableAltException;
    use Antlr\Antlr4\Runtime\PredictionContexts\PredictionContextCache;
    use Antlr\Antlr4\Runtime\Error\Exceptions\RecognitionException;
    use Antlr\Antlr4\Runtime\RuleContext;
    use Antlr\Antlr4\Runtime\Token;
    use Antlr\Antlr4\Runtime\TokenStream;
    use Antlr\Antlr4\Runtime\Vocabulary;
    use Antlr\Antlr4\Runtime\VocabularyImpl;
    use Antlr\Antlr4\Runtime\RuntimeMetaData;
    use Antlr\Antlr4\Runtime\Parser;

    final class JsonPathParser extends Parser
    {
        public const STRING = 1, INT = 2, NUMBER = 3, BOOLEAN = 4, NULL = 5, NAME = 6,
            OP_ROOT = 7, OP_WILDCARD = 8, OP_SLICE = 9, OP_FILTER = 10,
            OP_NOT = 11, OP_PATH = 12, OP_RECURSE = 13, OP_CURRENT = 14,
            OP_COMMA = 15, OP_OR = 16, OP_AND = 17, OP_COMP = 18, OP_PAREN_OPEN = 19,
            OP_PAREN_CLOSE = 20, OP_BRACK_OPEN = 21, OP_BRACK_CLOSE = 22,
            OP_QUOTE_SINGLE = 23, OP_QUOTE_DOUBLE = 24, S = 25;

        public const RULE_query = 0, RULE_rootQuery = 1, RULE_relQuery = 2, RULE_filterQuery = 3,
            RULE_absSingularQuery = 4, RULE_relSingularQuery = 5, RULE_singularQuery = 6,
            RULE_logicalAndExpression = 7, RULE_logicalOrExpression = 8,
            RULE_testExpression = 9, RULE_comparisonExpression = 10,
            RULE_parenExpression = 11, RULE_basicExpression = 12, RULE_logicalExpression = 13,
            RULE_functionExpression = 14, RULE_nameSelector = 15, RULE_wildcardSelector = 16,
            RULE_sliceSelector = 17, RULE_indexSelector = 18, RULE_filterSelector = 19,
            RULE_selector = 20, RULE_segment = 21, RULE_childSegment = 22,
            RULE_descendantSegment = 23, RULE_singularSegment = 24, RULE_nameSegment = 25,
            RULE_indexSegment = 26, RULE_literal = 27, RULE_functionName = 28,
            RULE_memberName = 29, RULE_comparable = 30, RULE_functionArgument = 31,
            RULE_bracketedSelection = 32;

        /**
         * @var array<string>
         */
        public const RULE_NAMES = [
            'query', 'rootQuery', 'relQuery', 'filterQuery', 'absSingularQuery',
            'relSingularQuery', 'singularQuery', 'logicalAndExpression', 'logicalOrExpression',
            'testExpression', 'comparisonExpression', 'parenExpression', 'basicExpression',
            'logicalExpression', 'functionExpression', 'nameSelector', 'wildcardSelector',
            'sliceSelector', 'indexSelector', 'filterSelector', 'selector', 'segment',
            'childSegment', 'descendantSegment', 'singularSegment', 'nameSegment',
            'indexSegment', 'literal', 'functionName', 'memberName', 'comparable',
            'functionArgument', 'bracketedSelection'
        ];

        /**
         * @var array<string|null>
         */
        private const LITERAL_NAMES = [
            null, null, null, null, null, "'null'", null, "'\$'", "'*'", "':'",
            "'?'", "'!'", "'.'", "'..'", "'@'", "','", "'||'", "'&&'", null, "'('",
            "')'", "'['", "']'", "'''", "'\"'"
        ];

        /**
         * @var array<string>
         */
        private const SYMBOLIC_NAMES = [
            null, "STRING", "INT", "NUMBER", "BOOLEAN", "NULL", "NAME", "OP_ROOT",
            "OP_WILDCARD", "OP_SLICE", "OP_FILTER", "OP_NOT", "OP_PATH", "OP_RECURSE",
            "OP_CURRENT", "OP_COMMA", "OP_OR", "OP_AND", "OP_COMP", "OP_PAREN_OPEN",
            "OP_PAREN_CLOSE", "OP_BRACK_OPEN", "OP_BRACK_CLOSE", "OP_QUOTE_SINGLE",
            "OP_QUOTE_DOUBLE", "S"
        ];

        private const SERIALIZED_ATN =
            [4, 1, 25, 409, 2, 0, 7, 0, 2, 1, 7, 1, 2, 2, 7, 2, 2, 3, 7, 3, 2, 4,
                7, 4, 2, 5, 7, 5, 2, 6, 7, 6, 2, 7, 7, 7, 2, 8, 7, 8, 2, 9, 7, 9,
                2, 10, 7, 10, 2, 11, 7, 11, 2, 12, 7, 12, 2, 13, 7, 13, 2, 14, 7,
                14, 2, 15, 7, 15, 2, 16, 7, 16, 2, 17, 7, 17, 2, 18, 7, 18, 2, 19,
                7, 19, 2, 20, 7, 20, 2, 21, 7, 21, 2, 22, 7, 22, 2, 23, 7, 23, 2,
                24, 7, 24, 2, 25, 7, 25, 2, 26, 7, 26, 2, 27, 7, 27, 2, 28, 7, 28,
                2, 29, 7, 29, 2, 30, 7, 30, 2, 31, 7, 31, 2, 32, 7, 32, 1, 0, 1, 0,
                1, 0, 1, 1, 1, 1, 5, 1, 72, 8, 1, 10, 1, 12, 1, 75, 9, 1, 1, 1, 5,
                1, 78, 8, 1, 10, 1, 12, 1, 81, 9, 1, 1, 2, 1, 2, 5, 2, 85, 8, 2, 10,
                2, 12, 2, 88, 9, 2, 1, 2, 5, 2, 91, 8, 2, 10, 2, 12, 2, 94, 9, 2,
                1, 3, 1, 3, 3, 3, 98, 8, 3, 1, 4, 1, 4, 5, 4, 102, 8, 4, 10, 4, 12,
                4, 105, 9, 4, 1, 4, 5, 4, 108, 8, 4, 10, 4, 12, 4, 111, 9, 4, 1, 5,
                1, 5, 5, 5, 115, 8, 5, 10, 5, 12, 5, 118, 9, 5, 1, 5, 5, 5, 121, 8,
                5, 10, 5, 12, 5, 124, 9, 5, 1, 6, 1, 6, 3, 6, 128, 8, 6, 1, 7, 1,
                7, 5, 7, 132, 8, 7, 10, 7, 12, 7, 135, 9, 7, 1, 7, 1, 7, 5, 7, 139,
                8, 7, 10, 7, 12, 7, 142, 9, 7, 1, 7, 5, 7, 145, 8, 7, 10, 7, 12, 7,
                148, 9, 7, 1, 8, 1, 8, 5, 8, 152, 8, 8, 10, 8, 12, 8, 155, 9, 8, 1,
                8, 1, 8, 5, 8, 159, 8, 8, 10, 8, 12, 8, 162, 9, 8, 1, 8, 5, 8, 165,
                8, 8, 10, 8, 12, 8, 168, 9, 8, 1, 9, 1, 9, 3, 9, 172, 8, 9, 1, 9,
                1, 9, 3, 9, 176, 8, 9, 1, 10, 1, 10, 5, 10, 180, 8, 10, 10, 10, 12,
                10, 183, 9, 10, 1, 10, 1, 10, 5, 10, 187, 8, 10, 10, 10, 12, 10, 190,
                9, 10, 1, 10, 1, 10, 1, 11, 1, 11, 5, 11, 196, 8, 11, 10, 11, 12,
                11, 199, 9, 11, 3, 11, 201, 8, 11, 1, 11, 1, 11, 5, 11, 205, 8, 11,
                10, 11, 12, 11, 208, 9, 11, 1, 11, 1, 11, 5, 11, 212, 8, 11, 10, 11,
                12, 11, 215, 9, 11, 1, 11, 1, 11, 1, 12, 1, 12, 1, 12, 3, 12, 222,
                8, 12, 1, 13, 1, 13, 1, 14, 1, 14, 1, 14, 5, 14, 229, 8, 14, 10, 14,
                12, 14, 232, 9, 14, 1, 14, 1, 14, 5, 14, 236, 8, 14, 10, 14, 12, 14,
                239, 9, 14, 1, 14, 1, 14, 5, 14, 243, 8, 14, 10, 14, 12, 14, 246,
                9, 14, 1, 14, 5, 14, 249, 8, 14, 10, 14, 12, 14, 252, 9, 14, 3, 14,
                254, 8, 14, 1, 14, 5, 14, 257, 8, 14, 10, 14, 12, 14, 260, 9, 14,
                1, 14, 1, 14, 1, 15, 1, 15, 1, 16, 1, 16, 1, 17, 1, 17, 5, 17, 270,
                8, 17, 10, 17, 12, 17, 273, 9, 17, 3, 17, 275, 8, 17, 1, 17, 1, 17,
                5, 17, 279, 8, 17, 10, 17, 12, 17, 282, 9, 17, 1, 17, 1, 17, 5, 17,
                286, 8, 17, 10, 17, 12, 17, 289, 9, 17, 3, 17, 291, 8, 17, 1, 17,
                1, 17, 5, 17, 295, 8, 17, 10, 17, 12, 17, 298, 9, 17, 1, 17, 3, 17,
                301, 8, 17, 3, 17, 303, 8, 17, 1, 18, 1, 18, 1, 19, 1, 19, 5, 19,
                309, 8, 19, 10, 19, 12, 19, 312, 9, 19, 1, 19, 1, 19, 1, 20, 1, 20,
                1, 20, 1, 20, 1, 20, 3, 20, 321, 8, 20, 1, 21, 1, 21, 3, 21, 325,
                8, 21, 1, 22, 1, 22, 1, 22, 1, 22, 3, 22, 331, 8, 22, 3, 22, 333,
                8, 22, 1, 23, 1, 23, 1, 23, 1, 23, 3, 23, 339, 8, 23, 1, 24, 1, 24,
                3, 24, 343, 8, 24, 1, 25, 1, 25, 1, 25, 1, 25, 1, 25, 1, 25, 3, 25,
                351, 8, 25, 1, 26, 1, 26, 1, 26, 1, 26, 1, 27, 1, 27, 1, 28, 1, 28,
                1, 29, 1, 29, 1, 30, 1, 30, 1, 30, 3, 30, 366, 8, 30, 1, 31, 1, 31,
                1, 31, 1, 31, 3, 31, 372, 8, 31, 1, 32, 1, 32, 5, 32, 376, 8, 32,
                10, 32, 12, 32, 379, 9, 32, 1, 32, 1, 32, 5, 32, 383, 8, 32, 10, 32,
                12, 32, 386, 9, 32, 1, 32, 1, 32, 5, 32, 390, 8, 32, 10, 32, 12, 32,
                393, 9, 32, 1, 32, 5, 32, 396, 8, 32, 10, 32, 12, 32, 399, 9, 32,
                1, 32, 5, 32, 402, 8, 32, 10, 32, 12, 32, 405, 9, 32, 1, 32, 1, 32,
                1, 32, 0, 0, 33, 0, 2, 4, 6, 8, 10, 12, 14, 16, 18, 20, 22, 24, 26,
                28, 30, 32, 34, 36, 38, 40, 42, 44, 46, 48, 50, 52, 54, 56, 58, 60,
                62, 64, 0, 1, 1, 0, 1, 5, 437, 0, 66, 1, 0, 0, 0, 2, 69, 1, 0, 0,
                0, 4, 82, 1, 0, 0, 0, 6, 97, 1, 0, 0, 0, 8, 99, 1, 0, 0, 0, 10, 112,
                1, 0, 0, 0, 12, 127, 1, 0, 0, 0, 14, 129, 1, 0, 0, 0, 16, 149, 1,
                0, 0, 0, 18, 171, 1, 0, 0, 0, 20, 177, 1, 0, 0, 0, 22, 200, 1, 0,
                0, 0, 24, 221, 1, 0, 0, 0, 26, 223, 1, 0, 0, 0, 28, 225, 1, 0, 0,
                0, 30, 263, 1, 0, 0, 0, 32, 265, 1, 0, 0, 0, 34, 274, 1, 0, 0, 0,
                36, 304, 1, 0, 0, 0, 38, 306, 1, 0, 0, 0, 40, 320, 1, 0, 0, 0, 42,
                324, 1, 0, 0, 0, 44, 332, 1, 0, 0, 0, 46, 334, 1, 0, 0, 0, 48, 342,
                1, 0, 0, 0, 50, 350, 1, 0, 0, 0, 52, 352, 1, 0, 0, 0, 54, 356, 1,
                0, 0, 0, 56, 358, 1, 0, 0, 0, 58, 360, 1, 0, 0, 0, 60, 365, 1, 0,
                0, 0, 62, 371, 1, 0, 0, 0, 64, 373, 1, 0, 0, 0, 66, 67, 3, 2, 1, 0,
                67, 68, 5, 0, 0, 1, 68, 1, 1, 0, 0, 0, 69, 79, 5, 7, 0, 0, 70, 72,
                5, 25, 0, 0, 71, 70, 1, 0, 0, 0, 72, 75, 1, 0, 0, 0, 73, 71, 1, 0,
                0, 0, 73, 74, 1, 0, 0, 0, 74, 76, 1, 0, 0, 0, 75, 73, 1, 0, 0, 0,
                76, 78, 3, 42, 21, 0, 77, 73, 1, 0, 0, 0, 78, 81, 1, 0, 0, 0, 79,
                77, 1, 0, 0, 0, 79, 80, 1, 0, 0, 0, 80, 3, 1, 0, 0, 0, 81, 79, 1,
                0, 0, 0, 82, 92, 5, 14, 0, 0, 83, 85, 5, 25, 0, 0, 84, 83, 1, 0, 0,
                0, 85, 88, 1, 0, 0, 0, 86, 84, 1, 0, 0, 0, 86, 87, 1, 0, 0, 0, 87,
                89, 1, 0, 0, 0, 88, 86, 1, 0, 0, 0, 89, 91, 3, 42, 21, 0, 90, 86,
                1, 0, 0, 0, 91, 94, 1, 0, 0, 0, 92, 90, 1, 0, 0, 0, 92, 93, 1, 0,
                0, 0, 93, 5, 1, 0, 0, 0, 94, 92, 1, 0, 0, 0, 95, 98, 3, 4, 2, 0, 96,
                98, 3, 2, 1, 0, 97, 95, 1, 0, 0, 0, 97, 96, 1, 0, 0, 0, 98, 7, 1,
                0, 0, 0, 99, 109, 5, 7, 0, 0, 100, 102, 5, 25, 0, 0, 101, 100, 1,
                0, 0, 0, 102, 105, 1, 0, 0, 0, 103, 101, 1, 0, 0, 0, 103, 104, 1,
                0, 0, 0, 104, 106, 1, 0, 0, 0, 105, 103, 1, 0, 0, 0, 106, 108, 3,
                48, 24, 0, 107, 103, 1, 0, 0, 0, 108, 111, 1, 0, 0, 0, 109, 107, 1,
                0, 0, 0, 109, 110, 1, 0, 0, 0, 110, 9, 1, 0, 0, 0, 111, 109, 1, 0,
                0, 0, 112, 122, 5, 14, 0, 0, 113, 115, 5, 25, 0, 0, 114, 113, 1, 0,
                0, 0, 115, 118, 1, 0, 0, 0, 116, 114, 1, 0, 0, 0, 116, 117, 1, 0,
                0, 0, 117, 119, 1, 0, 0, 0, 118, 116, 1, 0, 0, 0, 119, 121, 3, 48,
                24, 0, 120, 116, 1, 0, 0, 0, 121, 124, 1, 0, 0, 0, 122, 120, 1, 0,
                0, 0, 122, 123, 1, 0, 0, 0, 123, 11, 1, 0, 0, 0, 124, 122, 1, 0, 0,
                0, 125, 128, 3, 10, 5, 0, 126, 128, 3, 8, 4, 0, 127, 125, 1, 0, 0,
                0, 127, 126, 1, 0, 0, 0, 128, 13, 1, 0, 0, 0, 129, 146, 3, 24, 12,
                0, 130, 132, 5, 25, 0, 0, 131, 130, 1, 0, 0, 0, 132, 135, 1, 0, 0,
                0, 133, 131, 1, 0, 0, 0, 133, 134, 1, 0, 0, 0, 134, 136, 1, 0, 0,
                0, 135, 133, 1, 0, 0, 0, 136, 140, 5, 17, 0, 0, 137, 139, 5, 25, 0,
                0, 138, 137, 1, 0, 0, 0, 139, 142, 1, 0, 0, 0, 140, 138, 1, 0, 0,
                0, 140, 141, 1, 0, 0, 0, 141, 143, 1, 0, 0, 0, 142, 140, 1, 0, 0,
                0, 143, 145, 3, 24, 12, 0, 144, 133, 1, 0, 0, 0, 145, 148, 1, 0, 0,
                0, 146, 144, 1, 0, 0, 0, 146, 147, 1, 0, 0, 0, 147, 15, 1, 0, 0, 0,
                148, 146, 1, 0, 0, 0, 149, 166, 3, 14, 7, 0, 150, 152, 5, 25, 0, 0,
                151, 150, 1, 0, 0, 0, 152, 155, 1, 0, 0, 0, 153, 151, 1, 0, 0, 0,
                153, 154, 1, 0, 0, 0, 154, 156, 1, 0, 0, 0, 155, 153, 1, 0, 0, 0,
                156, 160, 5, 16, 0, 0, 157, 159, 5, 25, 0, 0, 158, 157, 1, 0, 0, 0,
                159, 162, 1, 0, 0, 0, 160, 158, 1, 0, 0, 0, 160, 161, 1, 0, 0, 0,
                161, 163, 1, 0, 0, 0, 162, 160, 1, 0, 0, 0, 163, 165, 3, 14, 7, 0,
                164, 153, 1, 0, 0, 0, 165, 168, 1, 0, 0, 0, 166, 164, 1, 0, 0, 0,
                166, 167, 1, 0, 0, 0, 167, 17, 1, 0, 0, 0, 168, 166, 1, 0, 0, 0, 169,
                170, 5, 11, 0, 0, 170, 172, 5, 25, 0, 0, 171, 169, 1, 0, 0, 0, 171,
                172, 1, 0, 0, 0, 172, 175, 1, 0, 0, 0, 173, 176, 3, 6, 3, 0, 174,
                176, 3, 28, 14, 0, 175, 173, 1, 0, 0, 0, 175, 174, 1, 0, 0, 0, 176,
                19, 1, 0, 0, 0, 177, 181, 3, 60, 30, 0, 178, 180, 5, 25, 0, 0, 179,
                178, 1, 0, 0, 0, 180, 183, 1, 0, 0, 0, 181, 179, 1, 0, 0, 0, 181,
                182, 1, 0, 0, 0, 182, 184, 1, 0, 0, 0, 183, 181, 1, 0, 0, 0, 184,
                188, 5, 18, 0, 0, 185, 187, 5, 25, 0, 0, 186, 185, 1, 0, 0, 0, 187,
                190, 1, 0, 0, 0, 188, 186, 1, 0, 0, 0, 188, 189, 1, 0, 0, 0, 189,
                191, 1, 0, 0, 0, 190, 188, 1, 0, 0, 0, 191, 192, 3, 60, 30, 0, 192,
                21, 1, 0, 0, 0, 193, 197, 5, 11, 0, 0, 194, 196, 5, 25, 0, 0, 195,
                194, 1, 0, 0, 0, 196, 199, 1, 0, 0, 0, 197, 195, 1, 0, 0, 0, 197,
                198, 1, 0, 0, 0, 198, 201, 1, 0, 0, 0, 199, 197, 1, 0, 0, 0, 200,
                193, 1, 0, 0, 0, 200, 201, 1, 0, 0, 0, 201, 202, 1, 0, 0, 0, 202,
                206, 5, 19, 0, 0, 203, 205, 5, 25, 0, 0, 204, 203, 1, 0, 0, 0, 205,
                208, 1, 0, 0, 0, 206, 204, 1, 0, 0, 0, 206, 207, 1, 0, 0, 0, 207,
                209, 1, 0, 0, 0, 208, 206, 1, 0, 0, 0, 209, 213, 3, 26, 13, 0, 210,
                212, 5, 25, 0, 0, 211, 210, 1, 0, 0, 0, 212, 215, 1, 0, 0, 0, 213,
                211, 1, 0, 0, 0, 213, 214, 1, 0, 0, 0, 214, 216, 1, 0, 0, 0, 215,
                213, 1, 0, 0, 0, 216, 217, 5, 20, 0, 0, 217, 23, 1, 0, 0, 0, 218,
                222, 3, 22, 11, 0, 219, 222, 3, 20, 10, 0, 220, 222, 3, 18, 9, 0,
                221, 218, 1, 0, 0, 0, 221, 219, 1, 0, 0, 0, 221, 220, 1, 0, 0, 0,
                222, 25, 1, 0, 0, 0, 223, 224, 3, 16, 8, 0, 224, 27, 1, 0, 0, 0, 225,
                226, 3, 56, 28, 0, 226, 230, 5, 19, 0, 0, 227, 229, 5, 25, 0, 0, 228,
                227, 1, 0, 0, 0, 229, 232, 1, 0, 0, 0, 230, 228, 1, 0, 0, 0, 230,
                231, 1, 0, 0, 0, 231, 253, 1, 0, 0, 0, 232, 230, 1, 0, 0, 0, 233,
                250, 3, 62, 31, 0, 234, 236, 5, 25, 0, 0, 235, 234, 1, 0, 0, 0, 236,
                239, 1, 0, 0, 0, 237, 235, 1, 0, 0, 0, 237, 238, 1, 0, 0, 0, 238,
                240, 1, 0, 0, 0, 239, 237, 1, 0, 0, 0, 240, 244, 5, 15, 0, 0, 241,
                243, 5, 25, 0, 0, 242, 241, 1, 0, 0, 0, 243, 246, 1, 0, 0, 0, 244,
                242, 1, 0, 0, 0, 244, 245, 1, 0, 0, 0, 245, 247, 1, 0, 0, 0, 246,
                244, 1, 0, 0, 0, 247, 249, 3, 62, 31, 0, 248, 237, 1, 0, 0, 0, 249,
                252, 1, 0, 0, 0, 250, 248, 1, 0, 0, 0, 250, 251, 1, 0, 0, 0, 251,
                254, 1, 0, 0, 0, 252, 250, 1, 0, 0, 0, 253, 233, 1, 0, 0, 0, 253,
                254, 1, 0, 0, 0, 254, 258, 1, 0, 0, 0, 255, 257, 5, 25, 0, 0, 256,
                255, 1, 0, 0, 0, 257, 260, 1, 0, 0, 0, 258, 256, 1, 0, 0, 0, 258,
                259, 1, 0, 0, 0, 259, 261, 1, 0, 0, 0, 260, 258, 1, 0, 0, 0, 261,
                262, 5, 20, 0, 0, 262, 29, 1, 0, 0, 0, 263, 264, 5, 1, 0, 0, 264,
                31, 1, 0, 0, 0, 265, 266, 5, 8, 0, 0, 266, 33, 1, 0, 0, 0, 267, 271,
                5, 2, 0, 0, 268, 270, 5, 25, 0, 0, 269, 268, 1, 0, 0, 0, 270, 273,
                1, 0, 0, 0, 271, 269, 1, 0, 0, 0, 271, 272, 1, 0, 0, 0, 272, 275,
                1, 0, 0, 0, 273, 271, 1, 0, 0, 0, 274, 267, 1, 0, 0, 0, 274, 275,
                1, 0, 0, 0, 275, 276, 1, 0, 0, 0, 276, 280, 5, 9, 0, 0, 277, 279,
                5, 25, 0, 0, 278, 277, 1, 0, 0, 0, 279, 282, 1, 0, 0, 0, 280, 278,
                1, 0, 0, 0, 280, 281, 1, 0, 0, 0, 281, 290, 1, 0, 0, 0, 282, 280,
                1, 0, 0, 0, 283, 287, 5, 2, 0, 0, 284, 286, 5, 25, 0, 0, 285, 284,
                1, 0, 0, 0, 286, 289, 1, 0, 0, 0, 287, 285, 1, 0, 0, 0, 287, 288,
                1, 0, 0, 0, 288, 291, 1, 0, 0, 0, 289, 287, 1, 0, 0, 0, 290, 283,
                1, 0, 0, 0, 290, 291, 1, 0, 0, 0, 291, 302, 1, 0, 0, 0, 292, 300,
                5, 9, 0, 0, 293, 295, 5, 25, 0, 0, 294, 293, 1, 0, 0, 0, 295, 298,
                1, 0, 0, 0, 296, 294, 1, 0, 0, 0, 296, 297, 1, 0, 0, 0, 297, 299,
                1, 0, 0, 0, 298, 296, 1, 0, 0, 0, 299, 301, 5, 2, 0, 0, 300, 296,
                1, 0, 0, 0, 300, 301, 1, 0, 0, 0, 301, 303, 1, 0, 0, 0, 302, 292,
                1, 0, 0, 0, 302, 303, 1, 0, 0, 0, 303, 35, 1, 0, 0, 0, 304, 305, 5,
                2, 0, 0, 305, 37, 1, 0, 0, 0, 306, 310, 5, 10, 0, 0, 307, 309, 5,
                25, 0, 0, 308, 307, 1, 0, 0, 0, 309, 312, 1, 0, 0, 0, 310, 308, 1,
                0, 0, 0, 310, 311, 1, 0, 0, 0, 311, 313, 1, 0, 0, 0, 312, 310, 1,
                0, 0, 0, 313, 314, 3, 26, 13, 0, 314, 39, 1, 0, 0, 0, 315, 321, 3,
                30, 15, 0, 316, 321, 3, 32, 16, 0, 317, 321, 3, 34, 17, 0, 318, 321,
                3, 36, 18, 0, 319, 321, 3, 38, 19, 0, 320, 315, 1, 0, 0, 0, 320, 316,
                1, 0, 0, 0, 320, 317, 1, 0, 0, 0, 320, 318, 1, 0, 0, 0, 320, 319,
                1, 0, 0, 0, 321, 41, 1, 0, 0, 0, 322, 325, 3, 44, 22, 0, 323, 325,
                3, 46, 23, 0, 324, 322, 1, 0, 0, 0, 324, 323, 1, 0, 0, 0, 325, 43,
                1, 0, 0, 0, 326, 333, 3, 64, 32, 0, 327, 330, 5, 12, 0, 0, 328, 331,
                3, 32, 16, 0, 329, 331, 3, 58, 29, 0, 330, 328, 1, 0, 0, 0, 330, 329,
                1, 0, 0, 0, 331, 333, 1, 0, 0, 0, 332, 326, 1, 0, 0, 0, 332, 327,
                1, 0, 0, 0, 333, 45, 1, 0, 0, 0, 334, 338, 5, 13, 0, 0, 335, 339,
                3, 64, 32, 0, 336, 339, 3, 32, 16, 0, 337, 339, 3, 58, 29, 0, 338,
                335, 1, 0, 0, 0, 338, 336, 1, 0, 0, 0, 338, 337, 1, 0, 0, 0, 339,
                47, 1, 0, 0, 0, 340, 343, 3, 50, 25, 0, 341, 343, 3, 52, 26, 0, 342,
                340, 1, 0, 0, 0, 342, 341, 1, 0, 0, 0, 343, 49, 1, 0, 0, 0, 344, 345,
                5, 21, 0, 0, 345, 346, 3, 30, 15, 0, 346, 347, 5, 22, 0, 0, 347, 351,
                1, 0, 0, 0, 348, 349, 5, 12, 0, 0, 349, 351, 3, 58, 29, 0, 350, 344,
                1, 0, 0, 0, 350, 348, 1, 0, 0, 0, 351, 51, 1, 0, 0, 0, 352, 353, 5,
                21, 0, 0, 353, 354, 3, 36, 18, 0, 354, 355, 5, 22, 0, 0, 355, 53,
                1, 0, 0, 0, 356, 357, 7, 0, 0, 0, 357, 55, 1, 0, 0, 0, 358, 359, 5,
                6, 0, 0, 359, 57, 1, 0, 0, 0, 360, 361, 5, 6, 0, 0, 361, 59, 1, 0,
                0, 0, 362, 366, 3, 54, 27, 0, 363, 366, 3, 12, 6, 0, 364, 366, 3,
                28, 14, 0, 365, 362, 1, 0, 0, 0, 365, 363, 1, 0, 0, 0, 365, 364, 1,
                0, 0, 0, 366, 61, 1, 0, 0, 0, 367, 372, 3, 54, 27, 0, 368, 372, 3,
                6, 3, 0, 369, 372, 3, 28, 14, 0, 370, 372, 3, 26, 13, 0, 371, 367,
                1, 0, 0, 0, 371, 368, 1, 0, 0, 0, 371, 369, 1, 0, 0, 0, 371, 370,
                1, 0, 0, 0, 372, 63, 1, 0, 0, 0, 373, 377, 5, 21, 0, 0, 374, 376,
                5, 25, 0, 0, 375, 374, 1, 0, 0, 0, 376, 379, 1, 0, 0, 0, 377, 375,
                1, 0, 0, 0, 377, 378, 1, 0, 0, 0, 378, 380, 1, 0, 0, 0, 379, 377,
                1, 0, 0, 0, 380, 397, 3, 40, 20, 0, 381, 383, 5, 25, 0, 0, 382, 381,
                1, 0, 0, 0, 383, 386, 1, 0, 0, 0, 384, 382, 1, 0, 0, 0, 384, 385,
                1, 0, 0, 0, 385, 387, 1, 0, 0, 0, 386, 384, 1, 0, 0, 0, 387, 391,
                5, 15, 0, 0, 388, 390, 5, 25, 0, 0, 389, 388, 1, 0, 0, 0, 390, 393,
                1, 0, 0, 0, 391, 389, 1, 0, 0, 0, 391, 392, 1, 0, 0, 0, 392, 394,
                1, 0, 0, 0, 393, 391, 1, 0, 0, 0, 394, 396, 3, 40, 20, 0, 395, 384,
                1, 0, 0, 0, 396, 399, 1, 0, 0, 0, 397, 395, 1, 0, 0, 0, 397, 398,
                1, 0, 0, 0, 398, 403, 1, 0, 0, 0, 399, 397, 1, 0, 0, 0, 400, 402,
                5, 25, 0, 0, 401, 400, 1, 0, 0, 0, 402, 405, 1, 0, 0, 0, 403, 401,
                1, 0, 0, 0, 403, 404, 1, 0, 0, 0, 404, 406, 1, 0, 0, 0, 405, 403,
                1, 0, 0, 0, 406, 407, 5, 22, 0, 0, 407, 65, 1, 0, 0, 0, 54, 73, 79,
                86, 92, 97, 103, 109, 116, 122, 127, 133, 140, 146, 153, 160, 166,
                171, 175, 181, 188, 197, 200, 206, 213, 221, 230, 237, 244, 250, 253,
                258, 271, 274, 280, 287, 290, 296, 300, 302, 310, 320, 324, 330, 332,
                338, 342, 350, 365, 371, 377, 384, 391, 397, 403];
        protected static $atn;
        protected static $decisionToDFA;
        protected static $sharedContextCache;

        public function __construct(TokenStream $input)
        {
            parent::__construct($input);

            self::initialize();

            $this->interp = new ParserATNSimulator($this, self::$atn, self::$decisionToDFA, self::$sharedContextCache);
        }

        private static function initialize(): void
        {
            if (self::$atn !== null) {
                return;
            }

            RuntimeMetaData::checkVersion('4.13.1', RuntimeMetaData::VERSION);

            $atn = (new ATNDeserializer())->deserialize(self::SERIALIZED_ATN);

            $decisionToDFA = [];
            for ($i = 0, $count = $atn->getNumberOfDecisions(); $i < $count; $i++) {
                $decisionToDFA[] = new DFA($atn->getDecisionState($i), $i);
            }

            self::$atn = $atn;
            self::$decisionToDFA = $decisionToDFA;
            self::$sharedContextCache = new PredictionContextCache();
        }

        public function getGrammarFileName(): string
        {
            return "JsonPathParser.g4";
        }

        public function getRuleNames(): array
        {
            return self::RULE_NAMES;
        }

        public function getSerializedATN(): array
        {
            return self::SERIALIZED_ATN;
        }

        public function getATN(): ATN
        {
            return self::$atn;
        }

        public function getVocabulary(): Vocabulary
        {
            static $vocabulary;

            return $vocabulary = $vocabulary ?? new VocabularyImpl(self::LITERAL_NAMES, self::SYMBOLIC_NAMES);
        }

        /**
         * @throws RecognitionException
         */
        public function query(): Context\QueryContext
        {
            $localContext = new Context\QueryContext($this->ctx, $this->getState());

            $this->enterRule($localContext, 0, self::RULE_query);

            try {
                $this->enterOuterAlt($localContext, 1);
                $this->setState(66);
                $this->rootQuery();
                $this->setState(67);
                $this->match(self::EOF);
            } catch (RecognitionException $exception) {
                $localContext->exception = $exception;
                $this->errorHandler->reportError($this, $exception);
                $this->errorHandler->recover($this, $exception);
            } finally {
                $this->exitRule();
            }

            return $localContext;
        }

        /**
         * @throws RecognitionException
         */
        public function rootQuery(): Context\RootQueryContext
        {
            $localContext = new Context\RootQueryContext($this->ctx, $this->getState());

            $this->enterRule($localContext, 2, self::RULE_rootQuery);

            try {
                $this->enterOuterAlt($localContext, 1);
                $this->setState(69);
                $this->match(self::OP_ROOT);
                $this->setState(79);
                $this->errorHandler->sync($this);

                $alt = $this->getInterpreter()->adaptivePredict($this->input, 1, $this->ctx);

                while ($alt !== 2 && $alt !== ATN::INVALID_ALT_NUMBER) {
                    if ($alt === 1) {
                        $this->setState(73);
                        $this->errorHandler->sync($this);

                        $_la = $this->input->LA(1);
                        while ($_la === self::S) {
                            $this->setState(70);
                            $this->match(self::S);
                            $this->setState(75);
                            $this->errorHandler->sync($this);
                            $_la = $this->input->LA(1);
                        }
                        $this->setState(76);
                        $this->segment();
                    }

                    $this->setState(81);
                    $this->errorHandler->sync($this);

                    $alt = $this->getInterpreter()->adaptivePredict($this->input, 1, $this->ctx);
                }
            } catch (RecognitionException $exception) {
                $localContext->exception = $exception;
                $this->errorHandler->reportError($this, $exception);
                $this->errorHandler->recover($this, $exception);
            } finally {
                $this->exitRule();
            }

            return $localContext;
        }

        /**
         * @throws RecognitionException
         */
        public function relQuery(): Context\RelQueryContext
        {
            $localContext = new Context\RelQueryContext($this->ctx, $this->getState());

            $this->enterRule($localContext, 4, self::RULE_relQuery);

            try {
                $this->enterOuterAlt($localContext, 1);
                $this->setState(82);
                $this->match(self::OP_CURRENT);
                $this->setState(92);
                $this->errorHandler->sync($this);

                $alt = $this->getInterpreter()->adaptivePredict($this->input, 3, $this->ctx);

                while ($alt !== 2 && $alt !== ATN::INVALID_ALT_NUMBER) {
                    if ($alt === 1) {
                        $this->setState(86);
                        $this->errorHandler->sync($this);

                        $_la = $this->input->LA(1);
                        while ($_la === self::S) {
                            $this->setState(83);
                            $this->match(self::S);
                            $this->setState(88);
                            $this->errorHandler->sync($this);
                            $_la = $this->input->LA(1);
                        }
                        $this->setState(89);
                        $this->segment();
                    }

                    $this->setState(94);
                    $this->errorHandler->sync($this);

                    $alt = $this->getInterpreter()->adaptivePredict($this->input, 3, $this->ctx);
                }
            } catch (RecognitionException $exception) {
                $localContext->exception = $exception;
                $this->errorHandler->reportError($this, $exception);
                $this->errorHandler->recover($this, $exception);
            } finally {
                $this->exitRule();
            }

            return $localContext;
        }

        /**
         * @throws RecognitionException
         */
        public function filterQuery(): Context\FilterQueryContext
        {
            $localContext = new Context\FilterQueryContext($this->ctx, $this->getState());

            $this->enterRule($localContext, 6, self::RULE_filterQuery);

            try {
                $this->setState(97);
                $this->errorHandler->sync($this);

                switch ($this->input->LA(1)) {
                    case self::OP_CURRENT:
                        $this->enterOuterAlt($localContext, 1);
                        $this->setState(95);
                        $this->relQuery();
                        break;

                    case self::OP_ROOT:
                        $this->enterOuterAlt($localContext, 2);
                        $this->setState(96);
                        $this->rootQuery();
                        break;

                    default:
                        throw new NoViableAltException($this);
                }
            } catch (RecognitionException $exception) {
                $localContext->exception = $exception;
                $this->errorHandler->reportError($this, $exception);
                $this->errorHandler->recover($this, $exception);
            } finally {
                $this->exitRule();
            }

            return $localContext;
        }

        /**
         * @throws RecognitionException
         */
        public function absSingularQuery(): Context\AbsSingularQueryContext
        {
            $localContext = new Context\AbsSingularQueryContext($this->ctx, $this->getState());

            $this->enterRule($localContext, 8, self::RULE_absSingularQuery);

            try {
                $this->enterOuterAlt($localContext, 1);
                $this->setState(99);
                $this->match(self::OP_ROOT);
                $this->setState(109);
                $this->errorHandler->sync($this);

                $alt = $this->getInterpreter()->adaptivePredict($this->input, 6, $this->ctx);

                while ($alt !== 2 && $alt !== ATN::INVALID_ALT_NUMBER) {
                    if ($alt === 1) {
                        $this->setState(103);
                        $this->errorHandler->sync($this);

                        $_la = $this->input->LA(1);
                        while ($_la === self::S) {
                            $this->setState(100);
                            $this->match(self::S);
                            $this->setState(105);
                            $this->errorHandler->sync($this);
                            $_la = $this->input->LA(1);
                        }
                        $this->setState(106);
                        $this->singularSegment();
                    }

                    $this->setState(111);
                    $this->errorHandler->sync($this);

                    $alt = $this->getInterpreter()->adaptivePredict($this->input, 6, $this->ctx);
                }
            } catch (RecognitionException $exception) {
                $localContext->exception = $exception;
                $this->errorHandler->reportError($this, $exception);
                $this->errorHandler->recover($this, $exception);
            } finally {
                $this->exitRule();
            }

            return $localContext;
        }

        /**
         * @throws RecognitionException
         */
        public function relSingularQuery(): Context\RelSingularQueryContext
        {
            $localContext = new Context\RelSingularQueryContext($this->ctx, $this->getState());

            $this->enterRule($localContext, 10, self::RULE_relSingularQuery);

            try {
                $this->enterOuterAlt($localContext, 1);
                $this->setState(112);
                $this->match(self::OP_CURRENT);
                $this->setState(122);
                $this->errorHandler->sync($this);

                $alt = $this->getInterpreter()->adaptivePredict($this->input, 8, $this->ctx);

                while ($alt !== 2 && $alt !== ATN::INVALID_ALT_NUMBER) {
                    if ($alt === 1) {
                        $this->setState(116);
                        $this->errorHandler->sync($this);

                        $_la = $this->input->LA(1);
                        while ($_la === self::S) {
                            $this->setState(113);
                            $this->match(self::S);
                            $this->setState(118);
                            $this->errorHandler->sync($this);
                            $_la = $this->input->LA(1);
                        }
                        $this->setState(119);
                        $this->singularSegment();
                    }

                    $this->setState(124);
                    $this->errorHandler->sync($this);

                    $alt = $this->getInterpreter()->adaptivePredict($this->input, 8, $this->ctx);
                }
            } catch (RecognitionException $exception) {
                $localContext->exception = $exception;
                $this->errorHandler->reportError($this, $exception);
                $this->errorHandler->recover($this, $exception);
            } finally {
                $this->exitRule();
            }

            return $localContext;
        }

        /**
         * @throws RecognitionException
         */
        public function singularQuery(): Context\SingularQueryContext
        {
            $localContext = new Context\SingularQueryContext($this->ctx, $this->getState());

            $this->enterRule($localContext, 12, self::RULE_singularQuery);

            try {
                $this->setState(127);
                $this->errorHandler->sync($this);

                switch ($this->input->LA(1)) {
                    case self::OP_CURRENT:
                        $this->enterOuterAlt($localContext, 1);
                        $this->setState(125);
                        $this->relSingularQuery();
                        break;

                    case self::OP_ROOT:
                        $this->enterOuterAlt($localContext, 2);
                        $this->setState(126);
                        $this->absSingularQuery();
                        break;

                    default:
                        throw new NoViableAltException($this);
                }
            } catch (RecognitionException $exception) {
                $localContext->exception = $exception;
                $this->errorHandler->reportError($this, $exception);
                $this->errorHandler->recover($this, $exception);
            } finally {
                $this->exitRule();
            }

            return $localContext;
        }

        /**
         * @throws RecognitionException
         */
        public function logicalAndExpression(): Context\LogicalAndExpressionContext
        {
            $localContext = new Context\LogicalAndExpressionContext($this->ctx, $this->getState());

            $this->enterRule($localContext, 14, self::RULE_logicalAndExpression);

            try {
                $this->enterOuterAlt($localContext, 1);
                $this->setState(129);
                $this->basicExpression();
                $this->setState(146);
                $this->errorHandler->sync($this);

                $alt = $this->getInterpreter()->adaptivePredict($this->input, 12, $this->ctx);

                while ($alt !== 2 && $alt !== ATN::INVALID_ALT_NUMBER) {
                    if ($alt === 1) {
                        $this->setState(133);
                        $this->errorHandler->sync($this);

                        $_la = $this->input->LA(1);
                        while ($_la === self::S) {
                            $this->setState(130);
                            $this->match(self::S);
                            $this->setState(135);
                            $this->errorHandler->sync($this);
                            $_la = $this->input->LA(1);
                        }
                        $this->setState(136);
                        $this->match(self::OP_AND);
                        $this->setState(140);
                        $this->errorHandler->sync($this);

                        $_la = $this->input->LA(1);
                        while ($_la === self::S) {
                            $this->setState(137);
                            $this->match(self::S);
                            $this->setState(142);
                            $this->errorHandler->sync($this);
                            $_la = $this->input->LA(1);
                        }
                        $this->setState(143);
                        $this->basicExpression();
                    }

                    $this->setState(148);
                    $this->errorHandler->sync($this);

                    $alt = $this->getInterpreter()->adaptivePredict($this->input, 12, $this->ctx);
                }
            } catch (RecognitionException $exception) {
                $localContext->exception = $exception;
                $this->errorHandler->reportError($this, $exception);
                $this->errorHandler->recover($this, $exception);
            } finally {
                $this->exitRule();
            }

            return $localContext;
        }

        /**
         * @throws RecognitionException
         */
        public function logicalOrExpression(): Context\LogicalOrExpressionContext
        {
            $localContext = new Context\LogicalOrExpressionContext($this->ctx, $this->getState());

            $this->enterRule($localContext, 16, self::RULE_logicalOrExpression);

            try {
                $this->enterOuterAlt($localContext, 1);
                $this->setState(149);
                $this->logicalAndExpression();
                $this->setState(166);
                $this->errorHandler->sync($this);

                $alt = $this->getInterpreter()->adaptivePredict($this->input, 15, $this->ctx);

                while ($alt !== 2 && $alt !== ATN::INVALID_ALT_NUMBER) {
                    if ($alt === 1) {
                        $this->setState(153);
                        $this->errorHandler->sync($this);

                        $_la = $this->input->LA(1);
                        while ($_la === self::S) {
                            $this->setState(150);
                            $this->match(self::S);
                            $this->setState(155);
                            $this->errorHandler->sync($this);
                            $_la = $this->input->LA(1);
                        }
                        $this->setState(156);
                        $this->match(self::OP_OR);
                        $this->setState(160);
                        $this->errorHandler->sync($this);

                        $_la = $this->input->LA(1);
                        while ($_la === self::S) {
                            $this->setState(157);
                            $this->match(self::S);
                            $this->setState(162);
                            $this->errorHandler->sync($this);
                            $_la = $this->input->LA(1);
                        }
                        $this->setState(163);
                        $this->logicalAndExpression();
                    }

                    $this->setState(168);
                    $this->errorHandler->sync($this);

                    $alt = $this->getInterpreter()->adaptivePredict($this->input, 15, $this->ctx);
                }
            } catch (RecognitionException $exception) {
                $localContext->exception = $exception;
                $this->errorHandler->reportError($this, $exception);
                $this->errorHandler->recover($this, $exception);
            } finally {
                $this->exitRule();
            }

            return $localContext;
        }

        /**
         * @throws RecognitionException
         */
        public function testExpression(): Context\TestExpressionContext
        {
            $localContext = new Context\TestExpressionContext($this->ctx, $this->getState());

            $this->enterRule($localContext, 18, self::RULE_testExpression);

            try {
                $this->enterOuterAlt($localContext, 1);
                $this->setState(171);
                $this->errorHandler->sync($this);
                $_la = $this->input->LA(1);

                if ($_la === self::OP_NOT) {
                    $this->setState(169);
                    $this->match(self::OP_NOT);
                    $this->setState(170);
                    $this->match(self::S);
                }
                $this->setState(175);
                $this->errorHandler->sync($this);

                switch ($this->input->LA(1)) {
                    case self::OP_ROOT:
                    case self::OP_CURRENT:
                        $this->setState(173);
                        $this->filterQuery();
                        break;

                    case self::NAME:
                        $this->setState(174);
                        $this->functionExpression();
                        break;

                    default:
                        throw new NoViableAltException($this);
                }
            } catch (RecognitionException $exception) {
                $localContext->exception = $exception;
                $this->errorHandler->reportError($this, $exception);
                $this->errorHandler->recover($this, $exception);
            } finally {
                $this->exitRule();
            }

            return $localContext;
        }

        /**
         * @throws RecognitionException
         */
        public function comparisonExpression(): Context\ComparisonExpressionContext
        {
            $localContext = new Context\ComparisonExpressionContext($this->ctx, $this->getState());

            $this->enterRule($localContext, 20, self::RULE_comparisonExpression);

            try {
                $this->enterOuterAlt($localContext, 1);
                $this->setState(177);
                $localContext->left = $this->comparable();
                $this->setState(181);
                $this->errorHandler->sync($this);

                $_la = $this->input->LA(1);
                while ($_la === self::S) {
                    $this->setState(178);
                    $this->match(self::S);
                    $this->setState(183);
                    $this->errorHandler->sync($this);
                    $_la = $this->input->LA(1);
                }
                $this->setState(184);
                $this->match(self::OP_COMP);
                $this->setState(188);
                $this->errorHandler->sync($this);

                $_la = $this->input->LA(1);
                while ($_la === self::S) {
                    $this->setState(185);
                    $this->match(self::S);
                    $this->setState(190);
                    $this->errorHandler->sync($this);
                    $_la = $this->input->LA(1);
                }
                $this->setState(191);
                $localContext->right = $this->comparable();
            } catch (RecognitionException $exception) {
                $localContext->exception = $exception;
                $this->errorHandler->reportError($this, $exception);
                $this->errorHandler->recover($this, $exception);
            } finally {
                $this->exitRule();
            }

            return $localContext;
        }

        /**
         * @throws RecognitionException
         */
        public function parenExpression(): Context\ParenExpressionContext
        {
            $localContext = new Context\ParenExpressionContext($this->ctx, $this->getState());

            $this->enterRule($localContext, 22, self::RULE_parenExpression);

            try {
                $this->enterOuterAlt($localContext, 1);
                $this->setState(200);
                $this->errorHandler->sync($this);
                $_la = $this->input->LA(1);

                if ($_la === self::OP_NOT) {
                    $this->setState(193);
                    $this->match(self::OP_NOT);
                    $this->setState(197);
                    $this->errorHandler->sync($this);

                    $_la = $this->input->LA(1);
                    while ($_la === self::S) {
                        $this->setState(194);
                        $this->match(self::S);
                        $this->setState(199);
                        $this->errorHandler->sync($this);
                        $_la = $this->input->LA(1);
                    }
                }
                $this->setState(202);
                $this->match(self::OP_PAREN_OPEN);
                $this->setState(206);
                $this->errorHandler->sync($this);

                $_la = $this->input->LA(1);
                while ($_la === self::S) {
                    $this->setState(203);
                    $this->match(self::S);
                    $this->setState(208);
                    $this->errorHandler->sync($this);
                    $_la = $this->input->LA(1);
                }
                $this->setState(209);
                $this->logicalExpression();
                $this->setState(213);
                $this->errorHandler->sync($this);

                $_la = $this->input->LA(1);
                while ($_la === self::S) {
                    $this->setState(210);
                    $this->match(self::S);
                    $this->setState(215);
                    $this->errorHandler->sync($this);
                    $_la = $this->input->LA(1);
                }
                $this->setState(216);
                $this->match(self::OP_PAREN_CLOSE);
            } catch (RecognitionException $exception) {
                $localContext->exception = $exception;
                $this->errorHandler->reportError($this, $exception);
                $this->errorHandler->recover($this, $exception);
            } finally {
                $this->exitRule();
            }

            return $localContext;
        }

        /**
         * @throws RecognitionException
         */
        public function basicExpression(): Context\BasicExpressionContext
        {
            $localContext = new Context\BasicExpressionContext($this->ctx, $this->getState());

            $this->enterRule($localContext, 24, self::RULE_basicExpression);

            try {
                $this->setState(221);
                $this->errorHandler->sync($this);

                switch ($this->getInterpreter()->adaptivePredict($this->input, 24, $this->ctx)) {
                    case 1:
                        $this->enterOuterAlt($localContext, 1);
                        $this->setState(218);
                        $this->parenExpression();
                        break;

                    case 2:
                        $this->enterOuterAlt($localContext, 2);
                        $this->setState(219);
                        $this->comparisonExpression();
                        break;

                    case 3:
                        $this->enterOuterAlt($localContext, 3);
                        $this->setState(220);
                        $this->testExpression();
                        break;
                }
            } catch (RecognitionException $exception) {
                $localContext->exception = $exception;
                $this->errorHandler->reportError($this, $exception);
                $this->errorHandler->recover($this, $exception);
            } finally {
                $this->exitRule();
            }

            return $localContext;
        }

        /**
         * @throws RecognitionException
         */
        public function logicalExpression(): Context\LogicalExpressionContext
        {
            $localContext = new Context\LogicalExpressionContext($this->ctx, $this->getState());

            $this->enterRule($localContext, 26, self::RULE_logicalExpression);

            try {
                $this->enterOuterAlt($localContext, 1);
                $this->setState(223);
                $this->logicalOrExpression();
            } catch (RecognitionException $exception) {
                $localContext->exception = $exception;
                $this->errorHandler->reportError($this, $exception);
                $this->errorHandler->recover($this, $exception);
            } finally {
                $this->exitRule();
            }

            return $localContext;
        }

        /**
         * @throws RecognitionException
         */
        public function functionExpression(): Context\FunctionExpressionContext
        {
            $localContext = new Context\FunctionExpressionContext($this->ctx, $this->getState());

            $this->enterRule($localContext, 28, self::RULE_functionExpression);

            try {
                $this->enterOuterAlt($localContext, 1);
                $this->setState(225);
                $this->functionName();
                $this->setState(226);
                $this->match(self::OP_PAREN_OPEN);
                $this->setState(230);
                $this->errorHandler->sync($this);

                $alt = $this->getInterpreter()->adaptivePredict($this->input, 25, $this->ctx);

                while ($alt !== 2 && $alt !== ATN::INVALID_ALT_NUMBER) {
                    if ($alt === 1) {
                        $this->setState(227);
                        $this->match(self::S);
                    }

                    $this->setState(232);
                    $this->errorHandler->sync($this);

                    $alt = $this->getInterpreter()->adaptivePredict($this->input, 25, $this->ctx);
                }
                $this->setState(253);
                $this->errorHandler->sync($this);
                $_la = $this->input->LA(1);

                if (((($_la) & ~0x3f) === 0 && ((1 << $_la) & 542974) !== 0)) {
                    $this->setState(233);
                    $this->functionArgument();
                    $this->setState(250);
                    $this->errorHandler->sync($this);

                    $alt = $this->getInterpreter()->adaptivePredict($this->input, 28, $this->ctx);

                    while ($alt !== 2 && $alt !== ATN::INVALID_ALT_NUMBER) {
                        if ($alt === 1) {
                            $this->setState(237);
                            $this->errorHandler->sync($this);

                            $_la = $this->input->LA(1);
                            while ($_la === self::S) {
                                $this->setState(234);
                                $this->match(self::S);
                                $this->setState(239);
                                $this->errorHandler->sync($this);
                                $_la = $this->input->LA(1);
                            }
                            $this->setState(240);
                            $this->match(self::OP_COMMA);
                            $this->setState(244);
                            $this->errorHandler->sync($this);

                            $_la = $this->input->LA(1);
                            while ($_la === self::S) {
                                $this->setState(241);
                                $this->match(self::S);
                                $this->setState(246);
                                $this->errorHandler->sync($this);
                                $_la = $this->input->LA(1);
                            }
                            $this->setState(247);
                            $this->functionArgument();
                        }

                        $this->setState(252);
                        $this->errorHandler->sync($this);

                        $alt = $this->getInterpreter()->adaptivePredict($this->input, 28, $this->ctx);
                    }
                }
                $this->setState(258);
                $this->errorHandler->sync($this);

                $_la = $this->input->LA(1);
                while ($_la === self::S) {
                    $this->setState(255);
                    $this->match(self::S);
                    $this->setState(260);
                    $this->errorHandler->sync($this);
                    $_la = $this->input->LA(1);
                }
                $this->setState(261);
                $this->match(self::OP_PAREN_CLOSE);
            } catch (RecognitionException $exception) {
                $localContext->exception = $exception;
                $this->errorHandler->reportError($this, $exception);
                $this->errorHandler->recover($this, $exception);
            } finally {
                $this->exitRule();
            }

            return $localContext;
        }

        /**
         * @throws RecognitionException
         */
        public function nameSelector(): Context\NameSelectorContext
        {
            $localContext = new Context\NameSelectorContext($this->ctx, $this->getState());

            $this->enterRule($localContext, 30, self::RULE_nameSelector);

            try {
                $this->enterOuterAlt($localContext, 1);
                $this->setState(263);
                $this->match(self::STRING);
            } catch (RecognitionException $exception) {
                $localContext->exception = $exception;
                $this->errorHandler->reportError($this, $exception);
                $this->errorHandler->recover($this, $exception);
            } finally {
                $this->exitRule();
            }

            return $localContext;
        }

        /**
         * @throws RecognitionException
         */
        public function wildcardSelector(): Context\WildcardSelectorContext
        {
            $localContext = new Context\WildcardSelectorContext($this->ctx, $this->getState());

            $this->enterRule($localContext, 32, self::RULE_wildcardSelector);

            try {
                $this->enterOuterAlt($localContext, 1);
                $this->setState(265);
                $this->match(self::OP_WILDCARD);
            } catch (RecognitionException $exception) {
                $localContext->exception = $exception;
                $this->errorHandler->reportError($this, $exception);
                $this->errorHandler->recover($this, $exception);
            } finally {
                $this->exitRule();
            }

            return $localContext;
        }

        /**
         * @throws RecognitionException
         */
        public function sliceSelector(): Context\SliceSelectorContext
        {
            $localContext = new Context\SliceSelectorContext($this->ctx, $this->getState());

            $this->enterRule($localContext, 34, self::RULE_sliceSelector);

            try {
                $this->enterOuterAlt($localContext, 1);
                $this->setState(274);
                $this->errorHandler->sync($this);
                $_la = $this->input->LA(1);

                if ($_la === self::INT) {
                    $this->setState(267);
                    $localContext->start = $this->match(self::INT);
                    $this->setState(271);
                    $this->errorHandler->sync($this);

                    $_la = $this->input->LA(1);
                    while ($_la === self::S) {
                        $this->setState(268);
                        $this->match(self::S);
                        $this->setState(273);
                        $this->errorHandler->sync($this);
                        $_la = $this->input->LA(1);
                    }
                }
                $this->setState(276);
                $this->match(self::OP_SLICE);
                $this->setState(280);
                $this->errorHandler->sync($this);

                $alt = $this->getInterpreter()->adaptivePredict($this->input, 33, $this->ctx);

                while ($alt !== 2 && $alt !== ATN::INVALID_ALT_NUMBER) {
                    if ($alt === 1) {
                        $this->setState(277);
                        $this->match(self::S);
                    }

                    $this->setState(282);
                    $this->errorHandler->sync($this);

                    $alt = $this->getInterpreter()->adaptivePredict($this->input, 33, $this->ctx);
                }
                $this->setState(290);
                $this->errorHandler->sync($this);
                $_la = $this->input->LA(1);

                if ($_la === self::INT) {
                    $this->setState(283);
                    $localContext->end = $this->match(self::INT);
                    $this->setState(287);
                    $this->errorHandler->sync($this);

                    $alt = $this->getInterpreter()->adaptivePredict($this->input, 34, $this->ctx);

                    while ($alt !== 2 && $alt !== ATN::INVALID_ALT_NUMBER) {
                        if ($alt === 1) {
                            $this->setState(284);
                            $this->match(self::S);
                        }

                        $this->setState(289);
                        $this->errorHandler->sync($this);

                        $alt = $this->getInterpreter()->adaptivePredict($this->input, 34, $this->ctx);
                    }
                }
                $this->setState(302);
                $this->errorHandler->sync($this);
                $_la = $this->input->LA(1);

                if ($_la === self::OP_SLICE) {
                    $this->setState(292);
                    $this->match(self::OP_SLICE);
                    $this->setState(300);
                    $this->errorHandler->sync($this);

                    switch ($this->getInterpreter()->adaptivePredict($this->input, 37, $this->ctx)) {
                        case 1:
                            $this->setState(296);
                            $this->errorHandler->sync($this);

                            $_la = $this->input->LA(1);
                            while ($_la === self::S) {
                                $this->setState(293);
                                $this->match(self::S);
                                $this->setState(298);
                                $this->errorHandler->sync($this);
                                $_la = $this->input->LA(1);
                            }
                            $this->setState(299);
                            $localContext->step = $this->match(self::INT);
                            break;
                    }
                }
            } catch (RecognitionException $exception) {
                $localContext->exception = $exception;
                $this->errorHandler->reportError($this, $exception);
                $this->errorHandler->recover($this, $exception);
            } finally {
                $this->exitRule();
            }

            return $localContext;
        }

        /**
         * @throws RecognitionException
         */
        public function indexSelector(): Context\IndexSelectorContext
        {
            $localContext = new Context\IndexSelectorContext($this->ctx, $this->getState());

            $this->enterRule($localContext, 36, self::RULE_indexSelector);

            try {
                $this->enterOuterAlt($localContext, 1);
                $this->setState(304);
                $this->match(self::INT);
            } catch (RecognitionException $exception) {
                $localContext->exception = $exception;
                $this->errorHandler->reportError($this, $exception);
                $this->errorHandler->recover($this, $exception);
            } finally {
                $this->exitRule();
            }

            return $localContext;
        }

        /**
         * @throws RecognitionException
         */
        public function filterSelector(): Context\FilterSelectorContext
        {
            $localContext = new Context\FilterSelectorContext($this->ctx, $this->getState());

            $this->enterRule($localContext, 38, self::RULE_filterSelector);

            try {
                $this->enterOuterAlt($localContext, 1);
                $this->setState(306);
                $this->match(self::OP_FILTER);
                $this->setState(310);
                $this->errorHandler->sync($this);

                $_la = $this->input->LA(1);
                while ($_la === self::S) {
                    $this->setState(307);
                    $this->match(self::S);
                    $this->setState(312);
                    $this->errorHandler->sync($this);
                    $_la = $this->input->LA(1);
                }
                $this->setState(313);
                $this->logicalExpression();
            } catch (RecognitionException $exception) {
                $localContext->exception = $exception;
                $this->errorHandler->reportError($this, $exception);
                $this->errorHandler->recover($this, $exception);
            } finally {
                $this->exitRule();
            }

            return $localContext;
        }

        /**
         * @throws RecognitionException
         */
        public function selector(): Context\SelectorContext
        {
            $localContext = new Context\SelectorContext($this->ctx, $this->getState());

            $this->enterRule($localContext, 40, self::RULE_selector);

            try {
                $this->setState(320);
                $this->errorHandler->sync($this);

                switch ($this->getInterpreter()->adaptivePredict($this->input, 40, $this->ctx)) {
                    case 1:
                        $this->enterOuterAlt($localContext, 1);
                        $this->setState(315);
                        $this->nameSelector();
                        break;

                    case 2:
                        $this->enterOuterAlt($localContext, 2);
                        $this->setState(316);
                        $this->wildcardSelector();
                        break;

                    case 3:
                        $this->enterOuterAlt($localContext, 3);
                        $this->setState(317);
                        $this->sliceSelector();
                        break;

                    case 4:
                        $this->enterOuterAlt($localContext, 4);
                        $this->setState(318);
                        $this->indexSelector();
                        break;

                    case 5:
                        $this->enterOuterAlt($localContext, 5);
                        $this->setState(319);
                        $this->filterSelector();
                        break;
                }
            } catch (RecognitionException $exception) {
                $localContext->exception = $exception;
                $this->errorHandler->reportError($this, $exception);
                $this->errorHandler->recover($this, $exception);
            } finally {
                $this->exitRule();
            }

            return $localContext;
        }

        /**
         * @throws RecognitionException
         */
        public function segment(): Context\SegmentContext
        {
            $localContext = new Context\SegmentContext($this->ctx, $this->getState());

            $this->enterRule($localContext, 42, self::RULE_segment);

            try {
                $this->setState(324);
                $this->errorHandler->sync($this);

                switch ($this->input->LA(1)) {
                    case self::OP_PATH:
                    case self::OP_BRACK_OPEN:
                        $this->enterOuterAlt($localContext, 1);
                        $this->setState(322);
                        $this->childSegment();
                        break;

                    case self::OP_RECURSE:
                        $this->enterOuterAlt($localContext, 2);
                        $this->setState(323);
                        $this->descendantSegment();
                        break;

                    default:
                        throw new NoViableAltException($this);
                }
            } catch (RecognitionException $exception) {
                $localContext->exception = $exception;
                $this->errorHandler->reportError($this, $exception);
                $this->errorHandler->recover($this, $exception);
            } finally {
                $this->exitRule();
            }

            return $localContext;
        }

        /**
         * @throws RecognitionException
         */
        public function childSegment(): Context\ChildSegmentContext
        {
            $localContext = new Context\ChildSegmentContext($this->ctx, $this->getState());

            $this->enterRule($localContext, 44, self::RULE_childSegment);

            try {
                $this->setState(332);
                $this->errorHandler->sync($this);

                switch ($this->input->LA(1)) {
                    case self::OP_BRACK_OPEN:
                        $this->enterOuterAlt($localContext, 1);
                        $this->setState(326);
                        $this->bracketedSelection();
                        break;

                    case self::OP_PATH:
                        $this->enterOuterAlt($localContext, 2);
                        $this->setState(327);
                        $this->match(self::OP_PATH);
                        $this->setState(330);
                        $this->errorHandler->sync($this);

                        switch ($this->input->LA(1)) {
                            case self::OP_WILDCARD:
                                $this->setState(328);
                                $this->wildcardSelector();
                                break;

                            case self::NAME:
                                $this->setState(329);
                                $this->memberName();
                                break;

                            default:
                                throw new NoViableAltException($this);
                        }
                        break;

                    default:
                        throw new NoViableAltException($this);
                }
            } catch (RecognitionException $exception) {
                $localContext->exception = $exception;
                $this->errorHandler->reportError($this, $exception);
                $this->errorHandler->recover($this, $exception);
            } finally {
                $this->exitRule();
            }

            return $localContext;
        }

        /**
         * @throws RecognitionException
         */
        public function descendantSegment(): Context\DescendantSegmentContext
        {
            $localContext = new Context\DescendantSegmentContext($this->ctx, $this->getState());

            $this->enterRule($localContext, 46, self::RULE_descendantSegment);

            try {
                $this->enterOuterAlt($localContext, 1);
                $this->setState(334);
                $this->match(self::OP_RECURSE);
                $this->setState(338);
                $this->errorHandler->sync($this);

                switch ($this->input->LA(1)) {
                    case self::OP_BRACK_OPEN:
                        $this->setState(335);
                        $this->bracketedSelection();
                        break;

                    case self::OP_WILDCARD:
                        $this->setState(336);
                        $this->wildcardSelector();
                        break;

                    case self::NAME:
                        $this->setState(337);
                        $this->memberName();
                        break;

                    default:
                        throw new NoViableAltException($this);
                }
            } catch (RecognitionException $exception) {
                $localContext->exception = $exception;
                $this->errorHandler->reportError($this, $exception);
                $this->errorHandler->recover($this, $exception);
            } finally {
                $this->exitRule();
            }

            return $localContext;
        }

        /**
         * @throws RecognitionException
         */
        public function singularSegment(): Context\SingularSegmentContext
        {
            $localContext = new Context\SingularSegmentContext($this->ctx, $this->getState());

            $this->enterRule($localContext, 48, self::RULE_singularSegment);

            try {
                $this->setState(342);
                $this->errorHandler->sync($this);

                switch ($this->getInterpreter()->adaptivePredict($this->input, 45, $this->ctx)) {
                    case 1:
                        $this->enterOuterAlt($localContext, 1);
                        $this->setState(340);
                        $this->nameSegment();
                        break;

                    case 2:
                        $this->enterOuterAlt($localContext, 2);
                        $this->setState(341);
                        $this->indexSegment();
                        break;
                }
            } catch (RecognitionException $exception) {
                $localContext->exception = $exception;
                $this->errorHandler->reportError($this, $exception);
                $this->errorHandler->recover($this, $exception);
            } finally {
                $this->exitRule();
            }

            return $localContext;
        }

        /**
         * @throws RecognitionException
         */
        public function nameSegment(): Context\NameSegmentContext
        {
            $localContext = new Context\NameSegmentContext($this->ctx, $this->getState());

            $this->enterRule($localContext, 50, self::RULE_nameSegment);

            try {
                $this->setState(350);
                $this->errorHandler->sync($this);

                switch ($this->input->LA(1)) {
                    case self::OP_BRACK_OPEN:
                        $this->enterOuterAlt($localContext, 1);
                        $this->setState(344);
                        $this->match(self::OP_BRACK_OPEN);
                        $this->setState(345);
                        $this->nameSelector();
                        $this->setState(346);
                        $this->match(self::OP_BRACK_CLOSE);
                        break;

                    case self::OP_PATH:
                        $this->enterOuterAlt($localContext, 2);
                        $this->setState(348);
                        $this->match(self::OP_PATH);
                        $this->setState(349);
                        $this->memberName();
                        break;

                    default:
                        throw new NoViableAltException($this);
                }
            } catch (RecognitionException $exception) {
                $localContext->exception = $exception;
                $this->errorHandler->reportError($this, $exception);
                $this->errorHandler->recover($this, $exception);
            } finally {
                $this->exitRule();
            }

            return $localContext;
        }

        /**
         * @throws RecognitionException
         */
        public function indexSegment(): Context\IndexSegmentContext
        {
            $localContext = new Context\IndexSegmentContext($this->ctx, $this->getState());

            $this->enterRule($localContext, 52, self::RULE_indexSegment);

            try {
                $this->enterOuterAlt($localContext, 1);
                $this->setState(352);
                $this->match(self::OP_BRACK_OPEN);
                $this->setState(353);
                $this->indexSelector();
                $this->setState(354);
                $this->match(self::OP_BRACK_CLOSE);
            } catch (RecognitionException $exception) {
                $localContext->exception = $exception;
                $this->errorHandler->reportError($this, $exception);
                $this->errorHandler->recover($this, $exception);
            } finally {
                $this->exitRule();
            }

            return $localContext;
        }

        /**
         * @throws RecognitionException
         */
        public function literal(): Context\LiteralContext
        {
            $localContext = new Context\LiteralContext($this->ctx, $this->getState());

            $this->enterRule($localContext, 54, self::RULE_literal);

            try {
                $this->enterOuterAlt($localContext, 1);
                $this->setState(356);

                $_la = $this->input->LA(1);

                if (!(((($_la) & ~0x3f) === 0 && ((1 << $_la) & 62) !== 0))) {
                    $this->errorHandler->recoverInline($this);
                } else {
                    if ($this->input->LA(1) === Token::EOF) {
                        $this->matchedEOF = true;
                    }

                    $this->errorHandler->reportMatch($this);
                    $this->consume();
                }
            } catch (RecognitionException $exception) {
                $localContext->exception = $exception;
                $this->errorHandler->reportError($this, $exception);
                $this->errorHandler->recover($this, $exception);
            } finally {
                $this->exitRule();
            }

            return $localContext;
        }

        /**
         * @throws RecognitionException
         */
        public function functionName(): Context\FunctionNameContext
        {
            $localContext = new Context\FunctionNameContext($this->ctx, $this->getState());

            $this->enterRule($localContext, 56, self::RULE_functionName);

            try {
                $this->enterOuterAlt($localContext, 1);
                $this->setState(358);
                $this->match(self::NAME);
            } catch (RecognitionException $exception) {
                $localContext->exception = $exception;
                $this->errorHandler->reportError($this, $exception);
                $this->errorHandler->recover($this, $exception);
            } finally {
                $this->exitRule();
            }

            return $localContext;
        }

        /**
         * @throws RecognitionException
         */
        public function memberName(): Context\MemberNameContext
        {
            $localContext = new Context\MemberNameContext($this->ctx, $this->getState());

            $this->enterRule($localContext, 58, self::RULE_memberName);

            try {
                $this->enterOuterAlt($localContext, 1);
                $this->setState(360);
                $this->match(self::NAME);
            } catch (RecognitionException $exception) {
                $localContext->exception = $exception;
                $this->errorHandler->reportError($this, $exception);
                $this->errorHandler->recover($this, $exception);
            } finally {
                $this->exitRule();
            }

            return $localContext;
        }

        /**
         * @throws RecognitionException
         */
        public function comparable(): Context\ComparableContext
        {
            $localContext = new Context\ComparableContext($this->ctx, $this->getState());

            $this->enterRule($localContext, 60, self::RULE_comparable);

            try {
                $this->setState(365);
                $this->errorHandler->sync($this);

                switch ($this->input->LA(1)) {
                    case self::STRING:
                    case self::INT:
                    case self::NUMBER:
                    case self::BOOLEAN:
                    case self::NULL:
                        $this->enterOuterAlt($localContext, 1);
                        $this->setState(362);
                        $this->literal();
                        break;

                    case self::OP_ROOT:
                    case self::OP_CURRENT:
                        $this->enterOuterAlt($localContext, 2);
                        $this->setState(363);
                        $this->singularQuery();
                        break;

                    case self::NAME:
                        $this->enterOuterAlt($localContext, 3);
                        $this->setState(364);
                        $this->functionExpression();
                        break;

                    default:
                        throw new NoViableAltException($this);
                }
            } catch (RecognitionException $exception) {
                $localContext->exception = $exception;
                $this->errorHandler->reportError($this, $exception);
                $this->errorHandler->recover($this, $exception);
            } finally {
                $this->exitRule();
            }

            return $localContext;
        }

        /**
         * @throws RecognitionException
         */
        public function functionArgument(): Context\FunctionArgumentContext
        {
            $localContext = new Context\FunctionArgumentContext($this->ctx, $this->getState());

            $this->enterRule($localContext, 62, self::RULE_functionArgument);

            try {
                $this->setState(371);
                $this->errorHandler->sync($this);

                switch ($this->getInterpreter()->adaptivePredict($this->input, 48, $this->ctx)) {
                    case 1:
                        $this->enterOuterAlt($localContext, 1);
                        $this->setState(367);
                        $this->literal();
                        break;

                    case 2:
                        $this->enterOuterAlt($localContext, 2);
                        $this->setState(368);
                        $this->filterQuery();
                        break;

                    case 3:
                        $this->enterOuterAlt($localContext, 3);
                        $this->setState(369);
                        $this->functionExpression();
                        break;

                    case 4:
                        $this->enterOuterAlt($localContext, 4);
                        $this->setState(370);
                        $this->logicalExpression();
                        break;
                }
            } catch (RecognitionException $exception) {
                $localContext->exception = $exception;
                $this->errorHandler->reportError($this, $exception);
                $this->errorHandler->recover($this, $exception);
            } finally {
                $this->exitRule();
            }

            return $localContext;
        }

        /**
         * @throws RecognitionException
         */
        public function bracketedSelection(): Context\BracketedSelectionContext
        {
            $localContext = new Context\BracketedSelectionContext($this->ctx, $this->getState());

            $this->enterRule($localContext, 64, self::RULE_bracketedSelection);

            try {
                $this->enterOuterAlt($localContext, 1);
                $this->setState(373);
                $this->match(self::OP_BRACK_OPEN);
                $this->setState(377);
                $this->errorHandler->sync($this);

                $_la = $this->input->LA(1);
                while ($_la === self::S) {
                    $this->setState(374);
                    $this->match(self::S);
                    $this->setState(379);
                    $this->errorHandler->sync($this);
                    $_la = $this->input->LA(1);
                }
                $this->setState(380);
                $this->selector();
                $this->setState(397);
                $this->errorHandler->sync($this);

                $alt = $this->getInterpreter()->adaptivePredict($this->input, 52, $this->ctx);

                while ($alt !== 2 && $alt !== ATN::INVALID_ALT_NUMBER) {
                    if ($alt === 1) {
                        $this->setState(384);
                        $this->errorHandler->sync($this);

                        $_la = $this->input->LA(1);
                        while ($_la === self::S) {
                            $this->setState(381);
                            $this->match(self::S);
                            $this->setState(386);
                            $this->errorHandler->sync($this);
                            $_la = $this->input->LA(1);
                        }
                        $this->setState(387);
                        $this->match(self::OP_COMMA);
                        $this->setState(391);
                        $this->errorHandler->sync($this);

                        $_la = $this->input->LA(1);
                        while ($_la === self::S) {
                            $this->setState(388);
                            $this->match(self::S);
                            $this->setState(393);
                            $this->errorHandler->sync($this);
                            $_la = $this->input->LA(1);
                        }
                        $this->setState(394);
                        $this->selector();
                    }

                    $this->setState(399);
                    $this->errorHandler->sync($this);

                    $alt = $this->getInterpreter()->adaptivePredict($this->input, 52, $this->ctx);
                }
                $this->setState(403);
                $this->errorHandler->sync($this);

                $_la = $this->input->LA(1);
                while ($_la === self::S) {
                    $this->setState(400);
                    $this->match(self::S);
                    $this->setState(405);
                    $this->errorHandler->sync($this);
                    $_la = $this->input->LA(1);
                }
                $this->setState(406);
                $this->match(self::OP_BRACK_CLOSE);
            } catch (RecognitionException $exception) {
                $localContext->exception = $exception;
                $this->errorHandler->reportError($this, $exception);
                $this->errorHandler->recover($this, $exception);
            } finally {
                $this->exitRule();
            }

            return $localContext;
        }
    }
}

namespace JsonScout\JsonPath\Parser\Context {
    use Antlr\Antlr4\Runtime\ParserRuleContext;
    use Antlr\Antlr4\Runtime\Token;
    use Antlr\Antlr4\Runtime\Tree\ParseTreeVisitor;
    use Antlr\Antlr4\Runtime\Tree\TerminalNode;
    use Antlr\Antlr4\Runtime\Tree\ParseTreeListener;
    use JsonScout\JsonPath\Parser\JsonPathParser;
    use JsonScout\JsonPath\Parser\JsonPathParserVisitor;

    class QueryContext extends ParserRuleContext
    {
        public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
        {
            parent::__construct($parent, $invokingState);
        }

        public function getRuleIndex(): int
        {
            return JsonPathParser::RULE_query;
        }

        public function rootQuery(): ?RootQueryContext
        {
            return $this->getTypedRuleContext(RootQueryContext::class, 0);
        }

        public function EOF(): ?TerminalNode
        {
            return $this->getToken(JsonPathParser::EOF, 0);
        }

        public function accept(ParseTreeVisitor $visitor): mixed
        {
            if ($visitor instanceof JsonPathParserVisitor) {
                return $visitor->visitQuery($this);
            }

            return $visitor->visitChildren($this);
        }
    }

    class RootQueryContext extends ParserRuleContext
    {
        public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
        {
            parent::__construct($parent, $invokingState);
        }

        public function getRuleIndex(): int
        {
            return JsonPathParser::RULE_rootQuery;
        }

        public function OP_ROOT(): ?TerminalNode
        {
            return $this->getToken(JsonPathParser::OP_ROOT, 0);
        }

        /**
         * @return array<SegmentContext>|SegmentContext|null
         */
        public function segment(?int $index = null)
        {
            if ($index === null) {
                return $this->getTypedRuleContexts(SegmentContext::class);
            }

            return $this->getTypedRuleContext(SegmentContext::class, $index);
        }

        /**
         * @return array<TerminalNode>|TerminalNode|null
         */
        public function S(?int $index = null)
        {
            if ($index === null) {
                return $this->getTokens(JsonPathParser::S);
            }

            return $this->getToken(JsonPathParser::S, $index);
        }

        public function accept(ParseTreeVisitor $visitor): mixed
        {
            if ($visitor instanceof JsonPathParserVisitor) {
                return $visitor->visitRootQuery($this);
            }

            return $visitor->visitChildren($this);
        }
    }

    class RelQueryContext extends ParserRuleContext
    {
        public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
        {
            parent::__construct($parent, $invokingState);
        }

        public function getRuleIndex(): int
        {
            return JsonPathParser::RULE_relQuery;
        }

        public function OP_CURRENT(): ?TerminalNode
        {
            return $this->getToken(JsonPathParser::OP_CURRENT, 0);
        }

        /**
         * @return array<SegmentContext>|SegmentContext|null
         */
        public function segment(?int $index = null)
        {
            if ($index === null) {
                return $this->getTypedRuleContexts(SegmentContext::class);
            }

            return $this->getTypedRuleContext(SegmentContext::class, $index);
        }

        /**
         * @return array<TerminalNode>|TerminalNode|null
         */
        public function S(?int $index = null)
        {
            if ($index === null) {
                return $this->getTokens(JsonPathParser::S);
            }

            return $this->getToken(JsonPathParser::S, $index);
        }

        public function accept(ParseTreeVisitor $visitor): mixed
        {
            if ($visitor instanceof JsonPathParserVisitor) {
                return $visitor->visitRelQuery($this);
            }

            return $visitor->visitChildren($this);
        }
    }

    class FilterQueryContext extends ParserRuleContext
    {
        public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
        {
            parent::__construct($parent, $invokingState);
        }

        public function getRuleIndex(): int
        {
            return JsonPathParser::RULE_filterQuery;
        }

        public function relQuery(): ?RelQueryContext
        {
            return $this->getTypedRuleContext(RelQueryContext::class, 0);
        }

        public function rootQuery(): ?RootQueryContext
        {
            return $this->getTypedRuleContext(RootQueryContext::class, 0);
        }

        public function accept(ParseTreeVisitor $visitor): mixed
        {
            if ($visitor instanceof JsonPathParserVisitor) {
                return $visitor->visitFilterQuery($this);
            }

            return $visitor->visitChildren($this);
        }
    }

    class AbsSingularQueryContext extends ParserRuleContext
    {
        public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
        {
            parent::__construct($parent, $invokingState);
        }

        public function getRuleIndex(): int
        {
            return JsonPathParser::RULE_absSingularQuery;
        }

        public function OP_ROOT(): ?TerminalNode
        {
            return $this->getToken(JsonPathParser::OP_ROOT, 0);
        }

        /**
         * @return array<SingularSegmentContext>|SingularSegmentContext|null
         */
        public function singularSegment(?int $index = null)
        {
            if ($index === null) {
                return $this->getTypedRuleContexts(SingularSegmentContext::class);
            }

            return $this->getTypedRuleContext(SingularSegmentContext::class, $index);
        }

        /**
         * @return array<TerminalNode>|TerminalNode|null
         */
        public function S(?int $index = null)
        {
            if ($index === null) {
                return $this->getTokens(JsonPathParser::S);
            }

            return $this->getToken(JsonPathParser::S, $index);
        }

        public function accept(ParseTreeVisitor $visitor): mixed
        {
            if ($visitor instanceof JsonPathParserVisitor) {
                return $visitor->visitAbsSingularQuery($this);
            }

            return $visitor->visitChildren($this);
        }
    }

    class RelSingularQueryContext extends ParserRuleContext
    {
        public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
        {
            parent::__construct($parent, $invokingState);
        }

        public function getRuleIndex(): int
        {
            return JsonPathParser::RULE_relSingularQuery;
        }

        public function OP_CURRENT(): ?TerminalNode
        {
            return $this->getToken(JsonPathParser::OP_CURRENT, 0);
        }

        /**
         * @return array<SingularSegmentContext>|SingularSegmentContext|null
         */
        public function singularSegment(?int $index = null)
        {
            if ($index === null) {
                return $this->getTypedRuleContexts(SingularSegmentContext::class);
            }

            return $this->getTypedRuleContext(SingularSegmentContext::class, $index);
        }

        /**
         * @return array<TerminalNode>|TerminalNode|null
         */
        public function S(?int $index = null)
        {
            if ($index === null) {
                return $this->getTokens(JsonPathParser::S);
            }

            return $this->getToken(JsonPathParser::S, $index);
        }

        public function accept(ParseTreeVisitor $visitor): mixed
        {
            if ($visitor instanceof JsonPathParserVisitor) {
                return $visitor->visitRelSingularQuery($this);
            }

            return $visitor->visitChildren($this);
        }
    }

    class SingularQueryContext extends ParserRuleContext
    {
        public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
        {
            parent::__construct($parent, $invokingState);
        }

        public function getRuleIndex(): int
        {
            return JsonPathParser::RULE_singularQuery;
        }

        public function relSingularQuery(): ?RelSingularQueryContext
        {
            return $this->getTypedRuleContext(RelSingularQueryContext::class, 0);
        }

        public function absSingularQuery(): ?AbsSingularQueryContext
        {
            return $this->getTypedRuleContext(AbsSingularQueryContext::class, 0);
        }

        public function accept(ParseTreeVisitor $visitor): mixed
        {
            if ($visitor instanceof JsonPathParserVisitor) {
                return $visitor->visitSingularQuery($this);
            }

            return $visitor->visitChildren($this);
        }
    }

    class LogicalAndExpressionContext extends ParserRuleContext
    {
        public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
        {
            parent::__construct($parent, $invokingState);
        }

        public function getRuleIndex(): int
        {
            return JsonPathParser::RULE_logicalAndExpression;
        }

        /**
         * @return array<BasicExpressionContext>|BasicExpressionContext|null
         */
        public function basicExpression(?int $index = null)
        {
            if ($index === null) {
                return $this->getTypedRuleContexts(BasicExpressionContext::class);
            }

            return $this->getTypedRuleContext(BasicExpressionContext::class, $index);
        }

        /**
         * @return array<TerminalNode>|TerminalNode|null
         */
        public function OP_AND(?int $index = null)
        {
            if ($index === null) {
                return $this->getTokens(JsonPathParser::OP_AND);
            }

            return $this->getToken(JsonPathParser::OP_AND, $index);
        }

        /**
         * @return array<TerminalNode>|TerminalNode|null
         */
        public function S(?int $index = null)
        {
            if ($index === null) {
                return $this->getTokens(JsonPathParser::S);
            }

            return $this->getToken(JsonPathParser::S, $index);
        }

        public function accept(ParseTreeVisitor $visitor): mixed
        {
            if ($visitor instanceof JsonPathParserVisitor) {
                return $visitor->visitLogicalAndExpression($this);
            }

            return $visitor->visitChildren($this);
        }
    }

    class LogicalOrExpressionContext extends ParserRuleContext
    {
        public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
        {
            parent::__construct($parent, $invokingState);
        }

        public function getRuleIndex(): int
        {
            return JsonPathParser::RULE_logicalOrExpression;
        }

        /**
         * @return array<LogicalAndExpressionContext>|LogicalAndExpressionContext|null
         */
        public function logicalAndExpression(?int $index = null)
        {
            if ($index === null) {
                return $this->getTypedRuleContexts(LogicalAndExpressionContext::class);
            }

            return $this->getTypedRuleContext(LogicalAndExpressionContext::class, $index);
        }

        /**
         * @return array<TerminalNode>|TerminalNode|null
         */
        public function OP_OR(?int $index = null)
        {
            if ($index === null) {
                return $this->getTokens(JsonPathParser::OP_OR);
            }

            return $this->getToken(JsonPathParser::OP_OR, $index);
        }

        /**
         * @return array<TerminalNode>|TerminalNode|null
         */
        public function S(?int $index = null)
        {
            if ($index === null) {
                return $this->getTokens(JsonPathParser::S);
            }

            return $this->getToken(JsonPathParser::S, $index);
        }

        public function accept(ParseTreeVisitor $visitor): mixed
        {
            if ($visitor instanceof JsonPathParserVisitor) {
                return $visitor->visitLogicalOrExpression($this);
            }

            return $visitor->visitChildren($this);
        }
    }

    class TestExpressionContext extends ParserRuleContext
    {
        public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
        {
            parent::__construct($parent, $invokingState);
        }

        public function getRuleIndex(): int
        {
            return JsonPathParser::RULE_testExpression;
        }

        public function filterQuery(): ?FilterQueryContext
        {
            return $this->getTypedRuleContext(FilterQueryContext::class, 0);
        }

        public function functionExpression(): ?FunctionExpressionContext
        {
            return $this->getTypedRuleContext(FunctionExpressionContext::class, 0);
        }

        public function OP_NOT(): ?TerminalNode
        {
            return $this->getToken(JsonPathParser::OP_NOT, 0);
        }

        public function S(): ?TerminalNode
        {
            return $this->getToken(JsonPathParser::S, 0);
        }

        public function accept(ParseTreeVisitor $visitor): mixed
        {
            if ($visitor instanceof JsonPathParserVisitor) {
                return $visitor->visitTestExpression($this);
            }

            return $visitor->visitChildren($this);
        }
    }

    class ComparisonExpressionContext extends ParserRuleContext
    {
        /**
         * @var ComparableContext|null $left
         */
        public ?ComparableContext $left;

        /**
         * @var ComparableContext|null $right
         */
        public ?ComparableContext $right;

        public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
        {
            parent::__construct($parent, $invokingState);
        }

        public function getRuleIndex(): int
        {
            return JsonPathParser::RULE_comparisonExpression;
        }

        public function OP_COMP(): ?TerminalNode
        {
            return $this->getToken(JsonPathParser::OP_COMP, 0);
        }

        /**
         * @return array<ComparableContext>|ComparableContext|null
         */
        public function comparable(?int $index = null)
        {
            if ($index === null) {
                return $this->getTypedRuleContexts(ComparableContext::class);
            }

            return $this->getTypedRuleContext(ComparableContext::class, $index);
        }

        /**
         * @return array<TerminalNode>|TerminalNode|null
         */
        public function S(?int $index = null)
        {
            if ($index === null) {
                return $this->getTokens(JsonPathParser::S);
            }

            return $this->getToken(JsonPathParser::S, $index);
        }

        public function accept(ParseTreeVisitor $visitor): mixed
        {
            if ($visitor instanceof JsonPathParserVisitor) {
                return $visitor->visitComparisonExpression($this);
            }

            return $visitor->visitChildren($this);
        }
    }

    class ParenExpressionContext extends ParserRuleContext
    {
        public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
        {
            parent::__construct($parent, $invokingState);
        }

        public function getRuleIndex(): int
        {
            return JsonPathParser::RULE_parenExpression;
        }

        public function OP_PAREN_OPEN(): ?TerminalNode
        {
            return $this->getToken(JsonPathParser::OP_PAREN_OPEN, 0);
        }

        public function logicalExpression(): ?LogicalExpressionContext
        {
            return $this->getTypedRuleContext(LogicalExpressionContext::class, 0);
        }

        public function OP_PAREN_CLOSE(): ?TerminalNode
        {
            return $this->getToken(JsonPathParser::OP_PAREN_CLOSE, 0);
        }

        public function OP_NOT(): ?TerminalNode
        {
            return $this->getToken(JsonPathParser::OP_NOT, 0);
        }

        /**
         * @return array<TerminalNode>|TerminalNode|null
         */
        public function S(?int $index = null)
        {
            if ($index === null) {
                return $this->getTokens(JsonPathParser::S);
            }

            return $this->getToken(JsonPathParser::S, $index);
        }

        public function accept(ParseTreeVisitor $visitor): mixed
        {
            if ($visitor instanceof JsonPathParserVisitor) {
                return $visitor->visitParenExpression($this);
            }

            return $visitor->visitChildren($this);
        }
    }

    class BasicExpressionContext extends ParserRuleContext
    {
        public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
        {
            parent::__construct($parent, $invokingState);
        }

        public function getRuleIndex(): int
        {
            return JsonPathParser::RULE_basicExpression;
        }

        public function parenExpression(): ?ParenExpressionContext
        {
            return $this->getTypedRuleContext(ParenExpressionContext::class, 0);
        }

        public function comparisonExpression(): ?ComparisonExpressionContext
        {
            return $this->getTypedRuleContext(ComparisonExpressionContext::class, 0);
        }

        public function testExpression(): ?TestExpressionContext
        {
            return $this->getTypedRuleContext(TestExpressionContext::class, 0);
        }

        public function accept(ParseTreeVisitor $visitor): mixed
        {
            if ($visitor instanceof JsonPathParserVisitor) {
                return $visitor->visitBasicExpression($this);
            }

            return $visitor->visitChildren($this);
        }
    }

    class LogicalExpressionContext extends ParserRuleContext
    {
        public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
        {
            parent::__construct($parent, $invokingState);
        }

        public function getRuleIndex(): int
        {
            return JsonPathParser::RULE_logicalExpression;
        }

        public function logicalOrExpression(): ?LogicalOrExpressionContext
        {
            return $this->getTypedRuleContext(LogicalOrExpressionContext::class, 0);
        }

        public function accept(ParseTreeVisitor $visitor): mixed
        {
            if ($visitor instanceof JsonPathParserVisitor) {
                return $visitor->visitLogicalExpression($this);
            }

            return $visitor->visitChildren($this);
        }
    }

    class FunctionExpressionContext extends ParserRuleContext
    {
        public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
        {
            parent::__construct($parent, $invokingState);
        }

        public function getRuleIndex(): int
        {
            return JsonPathParser::RULE_functionExpression;
        }

        public function functionName(): ?FunctionNameContext
        {
            return $this->getTypedRuleContext(FunctionNameContext::class, 0);
        }

        public function OP_PAREN_OPEN(): ?TerminalNode
        {
            return $this->getToken(JsonPathParser::OP_PAREN_OPEN, 0);
        }

        public function OP_PAREN_CLOSE(): ?TerminalNode
        {
            return $this->getToken(JsonPathParser::OP_PAREN_CLOSE, 0);
        }

        /**
         * @return array<TerminalNode>|TerminalNode|null
         */
        public function S(?int $index = null)
        {
            if ($index === null) {
                return $this->getTokens(JsonPathParser::S);
            }

            return $this->getToken(JsonPathParser::S, $index);
        }

        /**
         * @return array<FunctionArgumentContext>|FunctionArgumentContext|null
         */
        public function functionArgument(?int $index = null)
        {
            if ($index === null) {
                return $this->getTypedRuleContexts(FunctionArgumentContext::class);
            }

            return $this->getTypedRuleContext(FunctionArgumentContext::class, $index);
        }

        /**
         * @return array<TerminalNode>|TerminalNode|null
         */
        public function OP_COMMA(?int $index = null)
        {
            if ($index === null) {
                return $this->getTokens(JsonPathParser::OP_COMMA);
            }

            return $this->getToken(JsonPathParser::OP_COMMA, $index);
        }

        public function accept(ParseTreeVisitor $visitor): mixed
        {
            if ($visitor instanceof JsonPathParserVisitor) {
                return $visitor->visitFunctionExpression($this);
            }

            return $visitor->visitChildren($this);
        }
    }

    class NameSelectorContext extends ParserRuleContext
    {
        public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
        {
            parent::__construct($parent, $invokingState);
        }

        public function getRuleIndex(): int
        {
            return JsonPathParser::RULE_nameSelector;
        }

        public function STRING(): ?TerminalNode
        {
            return $this->getToken(JsonPathParser::STRING, 0);
        }

        public function accept(ParseTreeVisitor $visitor): mixed
        {
            if ($visitor instanceof JsonPathParserVisitor) {
                return $visitor->visitNameSelector($this);
            }

            return $visitor->visitChildren($this);
        }
    }

    class WildcardSelectorContext extends ParserRuleContext
    {
        public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
        {
            parent::__construct($parent, $invokingState);
        }

        public function getRuleIndex(): int
        {
            return JsonPathParser::RULE_wildcardSelector;
        }

        public function OP_WILDCARD(): ?TerminalNode
        {
            return $this->getToken(JsonPathParser::OP_WILDCARD, 0);
        }

        public function accept(ParseTreeVisitor $visitor): mixed
        {
            if ($visitor instanceof JsonPathParserVisitor) {
                return $visitor->visitWildcardSelector($this);
            }

            return $visitor->visitChildren($this);
        }
    }

    class SliceSelectorContext extends ParserRuleContext
    {
        /**
         * @var Token|null $start
         */
        public ?Token $start;

        /**
         * @var Token|null $end
         */
        public ?Token $end;

        /**
         * @var Token|null $step
         */
        public ?Token $step;

        public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
        {
            parent::__construct($parent, $invokingState);
        }

        public function getRuleIndex(): int
        {
            return JsonPathParser::RULE_sliceSelector;
        }

        /**
         * @return array<TerminalNode>|TerminalNode|null
         */
        public function OP_SLICE(?int $index = null)
        {
            if ($index === null) {
                return $this->getTokens(JsonPathParser::OP_SLICE);
            }

            return $this->getToken(JsonPathParser::OP_SLICE, $index);
        }

        /**
         * @return array<TerminalNode>|TerminalNode|null
         */
        public function S(?int $index = null)
        {
            if ($index === null) {
                return $this->getTokens(JsonPathParser::S);
            }

            return $this->getToken(JsonPathParser::S, $index);
        }

        /**
         * @return array<TerminalNode>|TerminalNode|null
         */
        public function INT(?int $index = null)
        {
            if ($index === null) {
                return $this->getTokens(JsonPathParser::INT);
            }

            return $this->getToken(JsonPathParser::INT, $index);
        }

        public function accept(ParseTreeVisitor $visitor): mixed
        {
            if ($visitor instanceof JsonPathParserVisitor) {
                return $visitor->visitSliceSelector($this);
            }

            return $visitor->visitChildren($this);
        }
    }

    class IndexSelectorContext extends ParserRuleContext
    {
        public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
        {
            parent::__construct($parent, $invokingState);
        }

        public function getRuleIndex(): int
        {
            return JsonPathParser::RULE_indexSelector;
        }

        public function INT(): ?TerminalNode
        {
            return $this->getToken(JsonPathParser::INT, 0);
        }

        public function accept(ParseTreeVisitor $visitor): mixed
        {
            if ($visitor instanceof JsonPathParserVisitor) {
                return $visitor->visitIndexSelector($this);
            }

            return $visitor->visitChildren($this);
        }
    }

    class FilterSelectorContext extends ParserRuleContext
    {
        public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
        {
            parent::__construct($parent, $invokingState);
        }

        public function getRuleIndex(): int
        {
            return JsonPathParser::RULE_filterSelector;
        }

        public function OP_FILTER(): ?TerminalNode
        {
            return $this->getToken(JsonPathParser::OP_FILTER, 0);
        }

        public function logicalExpression(): ?LogicalExpressionContext
        {
            return $this->getTypedRuleContext(LogicalExpressionContext::class, 0);
        }

        /**
         * @return array<TerminalNode>|TerminalNode|null
         */
        public function S(?int $index = null)
        {
            if ($index === null) {
                return $this->getTokens(JsonPathParser::S);
            }

            return $this->getToken(JsonPathParser::S, $index);
        }

        public function accept(ParseTreeVisitor $visitor): mixed
        {
            if ($visitor instanceof JsonPathParserVisitor) {
                return $visitor->visitFilterSelector($this);
            }

            return $visitor->visitChildren($this);
        }
    }

    class SelectorContext extends ParserRuleContext
    {
        public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
        {
            parent::__construct($parent, $invokingState);
        }

        public function getRuleIndex(): int
        {
            return JsonPathParser::RULE_selector;
        }

        public function nameSelector(): ?NameSelectorContext
        {
            return $this->getTypedRuleContext(NameSelectorContext::class, 0);
        }

        public function wildcardSelector(): ?WildcardSelectorContext
        {
            return $this->getTypedRuleContext(WildcardSelectorContext::class, 0);
        }

        public function sliceSelector(): ?SliceSelectorContext
        {
            return $this->getTypedRuleContext(SliceSelectorContext::class, 0);
        }

        public function indexSelector(): ?IndexSelectorContext
        {
            return $this->getTypedRuleContext(IndexSelectorContext::class, 0);
        }

        public function filterSelector(): ?FilterSelectorContext
        {
            return $this->getTypedRuleContext(FilterSelectorContext::class, 0);
        }

        public function accept(ParseTreeVisitor $visitor): mixed
        {
            if ($visitor instanceof JsonPathParserVisitor) {
                return $visitor->visitSelector($this);
            }

            return $visitor->visitChildren($this);
        }
    }

    class SegmentContext extends ParserRuleContext
    {
        public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
        {
            parent::__construct($parent, $invokingState);
        }

        public function getRuleIndex(): int
        {
            return JsonPathParser::RULE_segment;
        }

        public function childSegment(): ?ChildSegmentContext
        {
            return $this->getTypedRuleContext(ChildSegmentContext::class, 0);
        }

        public function descendantSegment(): ?DescendantSegmentContext
        {
            return $this->getTypedRuleContext(DescendantSegmentContext::class, 0);
        }

        public function accept(ParseTreeVisitor $visitor): mixed
        {
            if ($visitor instanceof JsonPathParserVisitor) {
                return $visitor->visitSegment($this);
            }

            return $visitor->visitChildren($this);
        }
    }

    class ChildSegmentContext extends ParserRuleContext
    {
        public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
        {
            parent::__construct($parent, $invokingState);
        }

        public function getRuleIndex(): int
        {
            return JsonPathParser::RULE_childSegment;
        }

        public function bracketedSelection(): ?BracketedSelectionContext
        {
            return $this->getTypedRuleContext(BracketedSelectionContext::class, 0);
        }

        public function OP_PATH(): ?TerminalNode
        {
            return $this->getToken(JsonPathParser::OP_PATH, 0);
        }

        public function wildcardSelector(): ?WildcardSelectorContext
        {
            return $this->getTypedRuleContext(WildcardSelectorContext::class, 0);
        }

        public function memberName(): ?MemberNameContext
        {
            return $this->getTypedRuleContext(MemberNameContext::class, 0);
        }

        public function accept(ParseTreeVisitor $visitor): mixed
        {
            if ($visitor instanceof JsonPathParserVisitor) {
                return $visitor->visitChildSegment($this);
            }

            return $visitor->visitChildren($this);
        }
    }

    class DescendantSegmentContext extends ParserRuleContext
    {
        public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
        {
            parent::__construct($parent, $invokingState);
        }

        public function getRuleIndex(): int
        {
            return JsonPathParser::RULE_descendantSegment;
        }

        public function OP_RECURSE(): ?TerminalNode
        {
            return $this->getToken(JsonPathParser::OP_RECURSE, 0);
        }

        public function bracketedSelection(): ?BracketedSelectionContext
        {
            return $this->getTypedRuleContext(BracketedSelectionContext::class, 0);
        }

        public function wildcardSelector(): ?WildcardSelectorContext
        {
            return $this->getTypedRuleContext(WildcardSelectorContext::class, 0);
        }

        public function memberName(): ?MemberNameContext
        {
            return $this->getTypedRuleContext(MemberNameContext::class, 0);
        }

        public function accept(ParseTreeVisitor $visitor): mixed
        {
            if ($visitor instanceof JsonPathParserVisitor) {
                return $visitor->visitDescendantSegment($this);
            }

            return $visitor->visitChildren($this);
        }
    }

    class SingularSegmentContext extends ParserRuleContext
    {
        public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
        {
            parent::__construct($parent, $invokingState);
        }

        public function getRuleIndex(): int
        {
            return JsonPathParser::RULE_singularSegment;
        }

        public function nameSegment(): ?NameSegmentContext
        {
            return $this->getTypedRuleContext(NameSegmentContext::class, 0);
        }

        public function indexSegment(): ?IndexSegmentContext
        {
            return $this->getTypedRuleContext(IndexSegmentContext::class, 0);
        }

        public function accept(ParseTreeVisitor $visitor): mixed
        {
            if ($visitor instanceof JsonPathParserVisitor) {
                return $visitor->visitSingularSegment($this);
            }

            return $visitor->visitChildren($this);
        }
    }

    class NameSegmentContext extends ParserRuleContext
    {
        public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
        {
            parent::__construct($parent, $invokingState);
        }

        public function getRuleIndex(): int
        {
            return JsonPathParser::RULE_nameSegment;
        }

        public function OP_BRACK_OPEN(): ?TerminalNode
        {
            return $this->getToken(JsonPathParser::OP_BRACK_OPEN, 0);
        }

        public function nameSelector(): ?NameSelectorContext
        {
            return $this->getTypedRuleContext(NameSelectorContext::class, 0);
        }

        public function OP_BRACK_CLOSE(): ?TerminalNode
        {
            return $this->getToken(JsonPathParser::OP_BRACK_CLOSE, 0);
        }

        public function OP_PATH(): ?TerminalNode
        {
            return $this->getToken(JsonPathParser::OP_PATH, 0);
        }

        public function memberName(): ?MemberNameContext
        {
            return $this->getTypedRuleContext(MemberNameContext::class, 0);
        }

        public function accept(ParseTreeVisitor $visitor): mixed
        {
            if ($visitor instanceof JsonPathParserVisitor) {
                return $visitor->visitNameSegment($this);
            }

            return $visitor->visitChildren($this);
        }
    }

    class IndexSegmentContext extends ParserRuleContext
    {
        public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
        {
            parent::__construct($parent, $invokingState);
        }

        public function getRuleIndex(): int
        {
            return JsonPathParser::RULE_indexSegment;
        }

        public function OP_BRACK_OPEN(): ?TerminalNode
        {
            return $this->getToken(JsonPathParser::OP_BRACK_OPEN, 0);
        }

        public function indexSelector(): ?IndexSelectorContext
        {
            return $this->getTypedRuleContext(IndexSelectorContext::class, 0);
        }

        public function OP_BRACK_CLOSE(): ?TerminalNode
        {
            return $this->getToken(JsonPathParser::OP_BRACK_CLOSE, 0);
        }

        public function accept(ParseTreeVisitor $visitor): mixed
        {
            if ($visitor instanceof JsonPathParserVisitor) {
                return $visitor->visitIndexSegment($this);
            }

            return $visitor->visitChildren($this);
        }
    }

    class LiteralContext extends ParserRuleContext
    {
        public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
        {
            parent::__construct($parent, $invokingState);
        }

        public function getRuleIndex(): int
        {
            return JsonPathParser::RULE_literal;
        }

        public function INT(): ?TerminalNode
        {
            return $this->getToken(JsonPathParser::INT, 0);
        }

        public function NUMBER(): ?TerminalNode
        {
            return $this->getToken(JsonPathParser::NUMBER, 0);
        }

        public function STRING(): ?TerminalNode
        {
            return $this->getToken(JsonPathParser::STRING, 0);
        }

        public function BOOLEAN(): ?TerminalNode
        {
            return $this->getToken(JsonPathParser::BOOLEAN, 0);
        }

        public function NULL(): ?TerminalNode
        {
            return $this->getToken(JsonPathParser::NULL, 0);
        }

        public function accept(ParseTreeVisitor $visitor): mixed
        {
            if ($visitor instanceof JsonPathParserVisitor) {
                return $visitor->visitLiteral($this);
            }

            return $visitor->visitChildren($this);
        }
    }

    class FunctionNameContext extends ParserRuleContext
    {
        public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
        {
            parent::__construct($parent, $invokingState);
        }

        public function getRuleIndex(): int
        {
            return JsonPathParser::RULE_functionName;
        }

        public function NAME(): ?TerminalNode
        {
            return $this->getToken(JsonPathParser::NAME, 0);
        }

        public function accept(ParseTreeVisitor $visitor): mixed
        {
            if ($visitor instanceof JsonPathParserVisitor) {
                return $visitor->visitFunctionName($this);
            }

            return $visitor->visitChildren($this);
        }
    }

    class MemberNameContext extends ParserRuleContext
    {
        public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
        {
            parent::__construct($parent, $invokingState);
        }

        public function getRuleIndex(): int
        {
            return JsonPathParser::RULE_memberName;
        }

        public function NAME(): ?TerminalNode
        {
            return $this->getToken(JsonPathParser::NAME, 0);
        }

        public function accept(ParseTreeVisitor $visitor): mixed
        {
            if ($visitor instanceof JsonPathParserVisitor) {
                return $visitor->visitMemberName($this);
            }

            return $visitor->visitChildren($this);
        }
    }

    class ComparableContext extends ParserRuleContext
    {
        public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
        {
            parent::__construct($parent, $invokingState);
        }

        public function getRuleIndex(): int
        {
            return JsonPathParser::RULE_comparable;
        }

        public function literal(): ?LiteralContext
        {
            return $this->getTypedRuleContext(LiteralContext::class, 0);
        }

        public function singularQuery(): ?SingularQueryContext
        {
            return $this->getTypedRuleContext(SingularQueryContext::class, 0);
        }

        public function functionExpression(): ?FunctionExpressionContext
        {
            return $this->getTypedRuleContext(FunctionExpressionContext::class, 0);
        }

        public function accept(ParseTreeVisitor $visitor): mixed
        {
            if ($visitor instanceof JsonPathParserVisitor) {
                return $visitor->visitComparable($this);
            }

            return $visitor->visitChildren($this);
        }
    }

    class FunctionArgumentContext extends ParserRuleContext
    {
        public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
        {
            parent::__construct($parent, $invokingState);
        }

        public function getRuleIndex(): int
        {
            return JsonPathParser::RULE_functionArgument;
        }

        public function literal(): ?LiteralContext
        {
            return $this->getTypedRuleContext(LiteralContext::class, 0);
        }

        public function filterQuery(): ?FilterQueryContext
        {
            return $this->getTypedRuleContext(FilterQueryContext::class, 0);
        }

        public function functionExpression(): ?FunctionExpressionContext
        {
            return $this->getTypedRuleContext(FunctionExpressionContext::class, 0);
        }

        public function logicalExpression(): ?LogicalExpressionContext
        {
            return $this->getTypedRuleContext(LogicalExpressionContext::class, 0);
        }

        public function accept(ParseTreeVisitor $visitor): mixed
        {
            if ($visitor instanceof JsonPathParserVisitor) {
                return $visitor->visitFunctionArgument($this);
            }

            return $visitor->visitChildren($this);
        }
    }

    class BracketedSelectionContext extends ParserRuleContext
    {
        public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
        {
            parent::__construct($parent, $invokingState);
        }

        public function getRuleIndex(): int
        {
            return JsonPathParser::RULE_bracketedSelection;
        }

        public function OP_BRACK_OPEN(): ?TerminalNode
        {
            return $this->getToken(JsonPathParser::OP_BRACK_OPEN, 0);
        }

        /**
         * @return array<SelectorContext>|SelectorContext|null
         */
        public function selector(?int $index = null)
        {
            if ($index === null) {
                return $this->getTypedRuleContexts(SelectorContext::class);
            }

            return $this->getTypedRuleContext(SelectorContext::class, $index);
        }

        public function OP_BRACK_CLOSE(): ?TerminalNode
        {
            return $this->getToken(JsonPathParser::OP_BRACK_CLOSE, 0);
        }

        /**
         * @return array<TerminalNode>|TerminalNode|null
         */
        public function S(?int $index = null)
        {
            if ($index === null) {
                return $this->getTokens(JsonPathParser::S);
            }

            return $this->getToken(JsonPathParser::S, $index);
        }

        /**
         * @return array<TerminalNode>|TerminalNode|null
         */
        public function OP_COMMA(?int $index = null)
        {
            if ($index === null) {
                return $this->getTokens(JsonPathParser::OP_COMMA);
            }

            return $this->getToken(JsonPathParser::OP_COMMA, $index);
        }

        public function accept(ParseTreeVisitor $visitor): mixed
        {
            if ($visitor instanceof JsonPathParserVisitor) {
                return $visitor->visitBracketedSelection($this);
            }

            return $visitor->visitChildren($this);
        }
    }
}