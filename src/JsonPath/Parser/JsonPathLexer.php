<?php

/*
 * Generated from d:/Development/Coding/antlr4/JsonPath/JsonPathLexer.g4 by ANTLR 4.13.1
 */

namespace JsonScout\JsonPath\Function\JsonPath\Parser {
	use Antlr\Antlr4\Runtime\Atn\ATNDeserializer;
	use Antlr\Antlr4\Runtime\Atn\LexerATNSimulator;
	use Antlr\Antlr4\Runtime\Lexer;
	use Antlr\Antlr4\Runtime\CharStream;
	use Antlr\Antlr4\Runtime\PredictionContexts\PredictionContextCache;
	use Antlr\Antlr4\Runtime\RuleContext;
	use Antlr\Antlr4\Runtime\Atn\ATN;
	use Antlr\Antlr4\Runtime\Dfa\DFA;
	use Antlr\Antlr4\Runtime\Vocabulary;
	use Antlr\Antlr4\Runtime\RuntimeMetaData;
	use Antlr\Antlr4\Runtime\VocabularyImpl;

	final class JsonPathLexer extends Lexer
	{
		public const STRING = 1, INT = 2, NUMBER = 3, BOOLEAN = 4, NULL = 5, NAME = 6, 
               OP_ROOT = 7, OP_WILDCARD = 8, OP_SLICE = 9, OP_FILTER = 10, 
               OP_NOT = 11, OP_PATH = 12, OP_RECURSE = 13, OP_CURRENT = 14, 
               OP_COMMA = 15, OP_OR = 16, OP_AND = 17, OP_COMP = 18, OP_PAREN_OPEN = 19, 
               OP_PAREN_CLOSE = 20, OP_BRACK_OPEN = 21, OP_BRACK_CLOSE = 22, 
               OP_QUOTE_SINGLE = 23, OP_QUOTE_DOUBLE = 24, S = 25;

		/**
		 * @var array<string>
		 */
		public const CHANNEL_NAMES = [
			'DEFAULT_TOKEN_CHANNEL', 'HIDDEN'
		];

		/**
		 * @var array<string>
		 */
		public const MODE_NAMES = [
			'DEFAULT_MODE'
		];

		/**
		 * @var array<string>
		 */
		public const RULE_NAMES = [
			'Digit', 'Digit1', 'Hexdig', 'Alpha', 'ESC', 'NonSurrogate', 'HighSurrogate', 
			'LowSurrogate', 'HexChar', 'Unescaped', 'Escapable', 'Fraction', 'Exponent', 
			'NameFirst', 'NameChar', 'TEXT_DOUBLE_QUOTED', 'TEXT_SINGLE_QUOTED', 
			'STRING', 'INT', 'NUMBER', 'BOOLEAN', 'NULL', 'NAME', 'OP_ROOT', 'OP_WILDCARD', 
			'OP_SLICE', 'OP_FILTER', 'OP_NOT', 'OP_PATH', 'OP_RECURSE', 'OP_CURRENT', 
			'OP_COMMA', 'OP_OR', 'OP_AND', 'OP_COMP', 'OP_PAREN_OPEN', 'OP_PAREN_CLOSE', 
			'OP_BRACK_OPEN', 'OP_BRACK_CLOSE', 'OP_QUOTE_SINGLE', 'OP_QUOTE_DOUBLE', 
			'S'
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
			[4, 0, 25, 300, 6, -1, 2, 0, 7, 0, 2, 1, 7, 1, 2, 2, 7, 2, 2, 3, 7, 3, 
		    2, 4, 7, 4, 2, 5, 7, 5, 2, 6, 7, 6, 2, 7, 7, 7, 2, 8, 7, 8, 2, 9, 
		    7, 9, 2, 10, 7, 10, 2, 11, 7, 11, 2, 12, 7, 12, 2, 13, 7, 13, 2, 14, 
		    7, 14, 2, 15, 7, 15, 2, 16, 7, 16, 2, 17, 7, 17, 2, 18, 7, 18, 2, 
		    19, 7, 19, 2, 20, 7, 20, 2, 21, 7, 21, 2, 22, 7, 22, 2, 23, 7, 23, 
		    2, 24, 7, 24, 2, 25, 7, 25, 2, 26, 7, 26, 2, 27, 7, 27, 2, 28, 7, 
		    28, 2, 29, 7, 29, 2, 30, 7, 30, 2, 31, 7, 31, 2, 32, 7, 32, 2, 33, 
		    7, 33, 2, 34, 7, 34, 2, 35, 7, 35, 2, 36, 7, 36, 2, 37, 7, 37, 2, 
		    38, 7, 38, 2, 39, 7, 39, 2, 40, 7, 40, 2, 41, 7, 41, 1, 0, 1, 0, 1, 
		    1, 1, 1, 1, 2, 1, 2, 1, 3, 1, 3, 1, 4, 1, 4, 1, 5, 1, 5, 3, 5, 98, 
		    8, 5, 1, 5, 1, 5, 1, 5, 1, 5, 1, 5, 1, 5, 1, 5, 1, 5, 1, 5, 3, 5, 
		    109, 8, 5, 1, 6, 1, 6, 1, 6, 1, 6, 1, 6, 1, 7, 1, 7, 1, 7, 1, 7, 1, 
		    7, 1, 8, 1, 8, 1, 8, 1, 8, 1, 8, 1, 8, 1, 8, 3, 8, 128, 8, 8, 1, 9, 
		    1, 9, 1, 10, 1, 10, 1, 10, 3, 10, 135, 8, 10, 1, 11, 1, 11, 5, 11, 
		    139, 8, 11, 10, 11, 12, 11, 142, 9, 11, 1, 12, 1, 12, 3, 12, 146, 
		    8, 12, 1, 12, 5, 12, 149, 8, 12, 10, 12, 12, 12, 152, 9, 12, 1, 13, 
		    1, 13, 3, 13, 156, 8, 13, 1, 14, 1, 14, 3, 14, 160, 8, 14, 1, 15, 
		    1, 15, 1, 15, 1, 15, 1, 15, 1, 15, 1, 15, 1, 15, 3, 15, 170, 8, 15, 
		    1, 16, 1, 16, 1, 16, 1, 16, 1, 16, 1, 16, 1, 16, 1, 16, 3, 16, 180, 
		    8, 16, 1, 17, 1, 17, 5, 17, 184, 8, 17, 10, 17, 12, 17, 187, 9, 17, 
		    1, 17, 1, 17, 1, 17, 1, 17, 5, 17, 193, 8, 17, 10, 17, 12, 17, 196, 
		    9, 17, 1, 17, 1, 17, 3, 17, 200, 8, 17, 1, 18, 1, 18, 3, 18, 204, 
		    8, 18, 1, 18, 1, 18, 5, 18, 208, 8, 18, 10, 18, 12, 18, 211, 9, 18, 
		    3, 18, 213, 8, 18, 1, 19, 1, 19, 1, 19, 3, 19, 218, 8, 19, 1, 19, 
		    3, 19, 221, 8, 19, 1, 19, 3, 19, 224, 8, 19, 1, 20, 1, 20, 1, 20, 
		    1, 20, 1, 20, 1, 20, 1, 20, 1, 20, 1, 20, 3, 20, 235, 8, 20, 1, 21, 
		    1, 21, 1, 21, 1, 21, 1, 21, 1, 22, 1, 22, 5, 22, 244, 8, 22, 10, 22, 
		    12, 22, 247, 9, 22, 1, 23, 1, 23, 1, 24, 1, 24, 1, 25, 1, 25, 1, 26, 
		    1, 26, 1, 27, 1, 27, 1, 28, 1, 28, 1, 29, 1, 29, 1, 29, 1, 30, 1, 
		    30, 1, 31, 1, 31, 1, 32, 1, 32, 1, 32, 1, 33, 1, 33, 1, 33, 1, 34, 
		    1, 34, 1, 34, 1, 34, 1, 34, 1, 34, 1, 34, 1, 34, 1, 34, 3, 34, 283, 
		    8, 34, 1, 35, 1, 35, 1, 36, 1, 36, 1, 37, 1, 37, 1, 38, 1, 38, 1, 
		    39, 1, 39, 1, 40, 1, 40, 1, 41, 1, 41, 1, 41, 1, 41, 0, 0, 42, 1, 
		    0, 3, 0, 5, 0, 7, 0, 9, 0, 11, 0, 13, 0, 15, 0, 17, 0, 19, 0, 21, 
		    0, 23, 0, 25, 0, 27, 0, 29, 0, 31, 0, 33, 0, 35, 1, 37, 2, 39, 3, 
		    41, 4, 43, 5, 45, 6, 47, 7, 49, 8, 51, 9, 53, 10, 55, 11, 57, 12, 
		    59, 13, 61, 14, 63, 15, 65, 16, 67, 17, 69, 18, 71, 19, 73, 20, 75, 
		    21, 77, 22, 79, 23, 81, 24, 83, 25, 1, 0, 16, 1, 0, 48, 57, 1, 0, 
		    49, 57, 2, 0, 48, 57, 65, 70, 2, 0, 65, 90, 97, 122, 4, 0, 65, 67, 
		    69, 70, 97, 99, 101, 102, 2, 0, 68, 68, 100, 100, 1, 0, 48, 55, 3, 
		    0, 56, 57, 65, 66, 97, 98, 2, 0, 67, 70, 99, 102, 5, 0, 32, 33, 35, 
		    38, 40, 91, 93, 55295, 57344, 65535, 4, 0, 8, 10, 12, 13, 47, 47, 
		    92, 92, 2, 0, 69, 69, 101, 101, 2, 0, 43, 43, 45, 45, 3, 0, 95, 95, 
		    128, 55295, 57344, 65535, 2, 0, 60, 60, 62, 62, 3, 0, 9, 10, 13, 13, 
		    32, 32, 312, 0, 35, 1, 0, 0, 0, 0, 37, 1, 0, 0, 0, 0, 39, 1, 0, 0, 
		    0, 0, 41, 1, 0, 0, 0, 0, 43, 1, 0, 0, 0, 0, 45, 1, 0, 0, 0, 0, 47, 
		    1, 0, 0, 0, 0, 49, 1, 0, 0, 0, 0, 51, 1, 0, 0, 0, 0, 53, 1, 0, 0, 
		    0, 0, 55, 1, 0, 0, 0, 0, 57, 1, 0, 0, 0, 0, 59, 1, 0, 0, 0, 0, 61, 
		    1, 0, 0, 0, 0, 63, 1, 0, 0, 0, 0, 65, 1, 0, 0, 0, 0, 67, 1, 0, 0, 
		    0, 0, 69, 1, 0, 0, 0, 0, 71, 1, 0, 0, 0, 0, 73, 1, 0, 0, 0, 0, 75, 
		    1, 0, 0, 0, 0, 77, 1, 0, 0, 0, 0, 79, 1, 0, 0, 0, 0, 81, 1, 0, 0, 
		    0, 0, 83, 1, 0, 0, 0, 1, 85, 1, 0, 0, 0, 3, 87, 1, 0, 0, 0, 5, 89, 
		    1, 0, 0, 0, 7, 91, 1, 0, 0, 0, 9, 93, 1, 0, 0, 0, 11, 108, 1, 0, 0, 
		    0, 13, 110, 1, 0, 0, 0, 15, 115, 1, 0, 0, 0, 17, 127, 1, 0, 0, 0, 
		    19, 129, 1, 0, 0, 0, 21, 134, 1, 0, 0, 0, 23, 136, 1, 0, 0, 0, 25, 
		    143, 1, 0, 0, 0, 27, 155, 1, 0, 0, 0, 29, 159, 1, 0, 0, 0, 31, 169, 
		    1, 0, 0, 0, 33, 179, 1, 0, 0, 0, 35, 199, 1, 0, 0, 0, 37, 212, 1, 
		    0, 0, 0, 39, 217, 1, 0, 0, 0, 41, 234, 1, 0, 0, 0, 43, 236, 1, 0, 
		    0, 0, 45, 241, 1, 0, 0, 0, 47, 248, 1, 0, 0, 0, 49, 250, 1, 0, 0, 
		    0, 51, 252, 1, 0, 0, 0, 53, 254, 1, 0, 0, 0, 55, 256, 1, 0, 0, 0, 
		    57, 258, 1, 0, 0, 0, 59, 260, 1, 0, 0, 0, 61, 263, 1, 0, 0, 0, 63, 
		    265, 1, 0, 0, 0, 65, 267, 1, 0, 0, 0, 67, 270, 1, 0, 0, 0, 69, 282, 
		    1, 0, 0, 0, 71, 284, 1, 0, 0, 0, 73, 286, 1, 0, 0, 0, 75, 288, 1, 
		    0, 0, 0, 77, 290, 1, 0, 0, 0, 79, 292, 1, 0, 0, 0, 81, 294, 1, 0, 
		    0, 0, 83, 296, 1, 0, 0, 0, 85, 86, 7, 0, 0, 0, 86, 2, 1, 0, 0, 0, 
		    87, 88, 7, 1, 0, 0, 88, 4, 1, 0, 0, 0, 89, 90, 7, 2, 0, 0, 90, 6, 
		    1, 0, 0, 0, 91, 92, 7, 3, 0, 0, 92, 8, 1, 0, 0, 0, 93, 94, 5, 92, 
		    0, 0, 94, 10, 1, 0, 0, 0, 95, 98, 3, 1, 0, 0, 96, 98, 7, 4, 0, 0, 
		    97, 95, 1, 0, 0, 0, 97, 96, 1, 0, 0, 0, 98, 99, 1, 0, 0, 0, 99, 100, 
		    3, 5, 2, 0, 100, 101, 3, 5, 2, 0, 101, 102, 3, 5, 2, 0, 102, 109, 
		    1, 0, 0, 0, 103, 104, 7, 5, 0, 0, 104, 105, 7, 6, 0, 0, 105, 106, 
		    3, 5, 2, 0, 106, 107, 3, 5, 2, 0, 107, 109, 1, 0, 0, 0, 108, 97, 1, 
		    0, 0, 0, 108, 103, 1, 0, 0, 0, 109, 12, 1, 0, 0, 0, 110, 111, 7, 5, 
		    0, 0, 111, 112, 7, 7, 0, 0, 112, 113, 3, 5, 2, 0, 113, 114, 3, 5, 
		    2, 0, 114, 14, 1, 0, 0, 0, 115, 116, 7, 5, 0, 0, 116, 117, 7, 8, 0, 
		    0, 117, 118, 3, 5, 2, 0, 118, 119, 3, 5, 2, 0, 119, 16, 1, 0, 0, 0, 
		    120, 128, 3, 11, 5, 0, 121, 122, 3, 13, 6, 0, 122, 123, 5, 92, 0, 
		    0, 123, 124, 5, 117, 0, 0, 124, 125, 1, 0, 0, 0, 125, 126, 3, 15, 
		    7, 0, 126, 128, 1, 0, 0, 0, 127, 120, 1, 0, 0, 0, 127, 121, 1, 0, 
		    0, 0, 128, 18, 1, 0, 0, 0, 129, 130, 7, 9, 0, 0, 130, 20, 1, 0, 0, 
		    0, 131, 135, 7, 10, 0, 0, 132, 133, 5, 117, 0, 0, 133, 135, 3, 17, 
		    8, 0, 134, 131, 1, 0, 0, 0, 134, 132, 1, 0, 0, 0, 135, 22, 1, 0, 0, 
		    0, 136, 140, 5, 46, 0, 0, 137, 139, 3, 1, 0, 0, 138, 137, 1, 0, 0, 
		    0, 139, 142, 1, 0, 0, 0, 140, 138, 1, 0, 0, 0, 140, 141, 1, 0, 0, 
		    0, 141, 24, 1, 0, 0, 0, 142, 140, 1, 0, 0, 0, 143, 145, 7, 11, 0, 
		    0, 144, 146, 7, 12, 0, 0, 145, 144, 1, 0, 0, 0, 145, 146, 1, 0, 0, 
		    0, 146, 150, 1, 0, 0, 0, 147, 149, 3, 1, 0, 0, 148, 147, 1, 0, 0, 
		    0, 149, 152, 1, 0, 0, 0, 150, 148, 1, 0, 0, 0, 150, 151, 1, 0, 0, 
		    0, 151, 26, 1, 0, 0, 0, 152, 150, 1, 0, 0, 0, 153, 156, 3, 7, 3, 0, 
		    154, 156, 7, 13, 0, 0, 155, 153, 1, 0, 0, 0, 155, 154, 1, 0, 0, 0, 
		    156, 28, 1, 0, 0, 0, 157, 160, 3, 1, 0, 0, 158, 160, 3, 27, 13, 0, 
		    159, 157, 1, 0, 0, 0, 159, 158, 1, 0, 0, 0, 160, 30, 1, 0, 0, 0, 161, 
		    170, 3, 19, 9, 0, 162, 170, 5, 39, 0, 0, 163, 164, 3, 9, 4, 0, 164, 
		    165, 5, 34, 0, 0, 165, 170, 1, 0, 0, 0, 166, 167, 3, 9, 4, 0, 167, 
		    168, 3, 21, 10, 0, 168, 170, 1, 0, 0, 0, 169, 161, 1, 0, 0, 0, 169, 
		    162, 1, 0, 0, 0, 169, 163, 1, 0, 0, 0, 169, 166, 1, 0, 0, 0, 170, 
		    32, 1, 0, 0, 0, 171, 180, 3, 19, 9, 0, 172, 180, 5, 34, 0, 0, 173, 
		    174, 3, 9, 4, 0, 174, 175, 5, 39, 0, 0, 175, 180, 1, 0, 0, 0, 176, 
		    177, 3, 9, 4, 0, 177, 178, 3, 21, 10, 0, 178, 180, 1, 0, 0, 0, 179, 
		    171, 1, 0, 0, 0, 179, 172, 1, 0, 0, 0, 179, 173, 1, 0, 0, 0, 179, 
		    176, 1, 0, 0, 0, 180, 34, 1, 0, 0, 0, 181, 185, 3, 79, 39, 0, 182, 
		    184, 3, 33, 16, 0, 183, 182, 1, 0, 0, 0, 184, 187, 1, 0, 0, 0, 185, 
		    183, 1, 0, 0, 0, 185, 186, 1, 0, 0, 0, 186, 188, 1, 0, 0, 0, 187, 
		    185, 1, 0, 0, 0, 188, 189, 3, 79, 39, 0, 189, 200, 1, 0, 0, 0, 190, 
		    194, 3, 81, 40, 0, 191, 193, 3, 31, 15, 0, 192, 191, 1, 0, 0, 0, 193, 
		    196, 1, 0, 0, 0, 194, 192, 1, 0, 0, 0, 194, 195, 1, 0, 0, 0, 195, 
		    197, 1, 0, 0, 0, 196, 194, 1, 0, 0, 0, 197, 198, 3, 81, 40, 0, 198, 
		    200, 1, 0, 0, 0, 199, 181, 1, 0, 0, 0, 199, 190, 1, 0, 0, 0, 200, 
		    36, 1, 0, 0, 0, 201, 213, 5, 48, 0, 0, 202, 204, 5, 45, 0, 0, 203, 
		    202, 1, 0, 0, 0, 203, 204, 1, 0, 0, 0, 204, 205, 1, 0, 0, 0, 205, 
		    209, 3, 3, 1, 0, 206, 208, 3, 1, 0, 0, 207, 206, 1, 0, 0, 0, 208, 
		    211, 1, 0, 0, 0, 209, 207, 1, 0, 0, 0, 209, 210, 1, 0, 0, 0, 210, 
		    213, 1, 0, 0, 0, 211, 209, 1, 0, 0, 0, 212, 201, 1, 0, 0, 0, 212, 
		    203, 1, 0, 0, 0, 213, 38, 1, 0, 0, 0, 214, 218, 3, 37, 18, 0, 215, 
		    216, 5, 45, 0, 0, 216, 218, 5, 48, 0, 0, 217, 214, 1, 0, 0, 0, 217, 
		    215, 1, 0, 0, 0, 218, 220, 1, 0, 0, 0, 219, 221, 3, 23, 11, 0, 220, 
		    219, 1, 0, 0, 0, 220, 221, 1, 0, 0, 0, 221, 223, 1, 0, 0, 0, 222, 
		    224, 3, 25, 12, 0, 223, 222, 1, 0, 0, 0, 223, 224, 1, 0, 0, 0, 224, 
		    40, 1, 0, 0, 0, 225, 226, 5, 116, 0, 0, 226, 227, 5, 114, 0, 0, 227, 
		    228, 5, 117, 0, 0, 228, 235, 5, 101, 0, 0, 229, 230, 5, 102, 0, 0, 
		    230, 231, 5, 97, 0, 0, 231, 232, 5, 108, 0, 0, 232, 233, 5, 115, 0, 
		    0, 233, 235, 5, 101, 0, 0, 234, 225, 1, 0, 0, 0, 234, 229, 1, 0, 0, 
		    0, 235, 42, 1, 0, 0, 0, 236, 237, 5, 110, 0, 0, 237, 238, 5, 117, 
		    0, 0, 238, 239, 5, 108, 0, 0, 239, 240, 5, 108, 0, 0, 240, 44, 1, 
		    0, 0, 0, 241, 245, 3, 27, 13, 0, 242, 244, 3, 29, 14, 0, 243, 242, 
		    1, 0, 0, 0, 244, 247, 1, 0, 0, 0, 245, 243, 1, 0, 0, 0, 245, 246, 
		    1, 0, 0, 0, 246, 46, 1, 0, 0, 0, 247, 245, 1, 0, 0, 0, 248, 249, 5, 
		    36, 0, 0, 249, 48, 1, 0, 0, 0, 250, 251, 5, 42, 0, 0, 251, 50, 1, 
		    0, 0, 0, 252, 253, 5, 58, 0, 0, 253, 52, 1, 0, 0, 0, 254, 255, 5, 
		    63, 0, 0, 255, 54, 1, 0, 0, 0, 256, 257, 5, 33, 0, 0, 257, 56, 1, 
		    0, 0, 0, 258, 259, 5, 46, 0, 0, 259, 58, 1, 0, 0, 0, 260, 261, 5, 
		    46, 0, 0, 261, 262, 5, 46, 0, 0, 262, 60, 1, 0, 0, 0, 263, 264, 5, 
		    64, 0, 0, 264, 62, 1, 0, 0, 0, 265, 266, 5, 44, 0, 0, 266, 64, 1, 
		    0, 0, 0, 267, 268, 5, 124, 0, 0, 268, 269, 5, 124, 0, 0, 269, 66, 
		    1, 0, 0, 0, 270, 271, 5, 38, 0, 0, 271, 272, 5, 38, 0, 0, 272, 68, 
		    1, 0, 0, 0, 273, 274, 5, 61, 0, 0, 274, 283, 5, 61, 0, 0, 275, 276, 
		    5, 33, 0, 0, 276, 283, 5, 61, 0, 0, 277, 278, 5, 60, 0, 0, 278, 283, 
		    5, 61, 0, 0, 279, 280, 5, 62, 0, 0, 280, 283, 5, 61, 0, 0, 281, 283, 
		    7, 14, 0, 0, 282, 273, 1, 0, 0, 0, 282, 275, 1, 0, 0, 0, 282, 277, 
		    1, 0, 0, 0, 282, 279, 1, 0, 0, 0, 282, 281, 1, 0, 0, 0, 283, 70, 1, 
		    0, 0, 0, 284, 285, 5, 40, 0, 0, 285, 72, 1, 0, 0, 0, 286, 287, 5, 
		    41, 0, 0, 287, 74, 1, 0, 0, 0, 288, 289, 5, 91, 0, 0, 289, 76, 1, 
		    0, 0, 0, 290, 291, 5, 93, 0, 0, 291, 78, 1, 0, 0, 0, 292, 293, 5, 
		    39, 0, 0, 293, 80, 1, 0, 0, 0, 294, 295, 5, 34, 0, 0, 295, 82, 1, 
		    0, 0, 0, 296, 297, 7, 15, 0, 0, 297, 298, 1, 0, 0, 0, 298, 299, 6, 
		    41, 0, 0, 299, 84, 1, 0, 0, 0, 24, 0, 97, 108, 127, 134, 140, 145, 
		    150, 155, 159, 169, 179, 185, 194, 199, 203, 209, 212, 217, 220, 223, 
		    234, 245, 282, 1, 6, 0, 0];
		protected static $atn;
		protected static $decisionToDFA;
		protected static $sharedContextCache;
		public function __construct(CharStream $input)
		{
			parent::__construct($input);

			self::initialize();

			$this->interp = new LexerATNSimulator($this, self::$atn, self::$decisionToDFA, self::$sharedContextCache);
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

		public static function vocabulary(): Vocabulary
		{
			static $vocabulary;

			return $vocabulary = $vocabulary ?? new VocabularyImpl(self::LITERAL_NAMES, self::SYMBOLIC_NAMES);
		}

		public function getGrammarFileName(): string
		{
			return 'JsonPathLexer.g4';
		}

		public function getRuleNames(): array
		{
			return self::RULE_NAMES;
		}

		public function getSerializedATN(): array
		{
			return self::SERIALIZED_ATN;
		}

		/**
		 * @return array<string>
		 */
		public function getChannelNames(): array
		{
			return self::CHANNEL_NAMES;
		}

		/**
		 * @return array<string>
		 */
		public function getModeNames(): array
		{
			return self::MODE_NAMES;
		}

		public function getATN(): ATN
		{
			return self::$atn;
		}

		public function getVocabulary(): Vocabulary
		{
			return self::vocabulary();
		}
	}
}