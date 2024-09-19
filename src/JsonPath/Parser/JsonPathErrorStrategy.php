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

use Antlr\Antlr4\Runtime\Error\DefaultErrorStrategy;
use Antlr\Antlr4\Runtime\Error\Exceptions\InputMismatchException;
use Antlr\Antlr4\Runtime\Error\Exceptions\NoViableAltException;
use Antlr\Antlr4\Runtime\IntervalSet;
use Antlr\Antlr4\Runtime\Parser;
use Antlr\Antlr4\Runtime\Token;
use Antlr\Antlr4\Runtime\Vocabulary;



class JsonPathErrorStrategy
    extends DefaultErrorStrategy
{
    //==================================================================================================================
    private static function getExpected(IntervalSet $expectedTokens, Vocabulary $vocab)
        : string
    {
        $expectedTokens->removeOne(JsonPathLexer::S);
        
        $expecteds = array_map(function(int $token) use($vocab)
        {
            return strtolower($vocab->getDisplayName($token));
        }, $expectedTokens->toArray());
        
        $result = "";
        
        foreach ($expecteds as $key => $id)
        {
            if ($key !== array_key_first($expecteds))
            {
                if ($key === array_key_last($expecteds))
                {
                    $result .= ' or ';
                }
                else
                {
                    $result .= ', ';
                }
            }

            $result .= $id;
        }
        
        return $result;
    }
    
    //==================================================================================================================
    #[\Override]
    protected function reportInputMismatch(Parser $recognizer, InputMismatchException $e)
        : void
    {
        $expected_tokens = $e->getExpectedTokens();
        
        if ($expected_tokens === null)
        {
            throw new \LogicException('unexpected null expected tokens');
        }
        
        $offending_token = $e->getOffendingToken();
        $msg = "mismatched input {$this->getTokenErrorDisplay($offending_token)} expecting "
               .self::getExpected($expected_tokens, $recognizer->getVocabulary());

        throw new ExceptionSyntaxError($msg,
            ($offending_token?->getLine() ?? -1),
            ($offending_token?->getCharPositionInLine() ?? -1)
        );
    }
    
    #[\Override]
    protected function reportUnwantedToken(Parser $recognizer)
        : void
    {
        if ($this->inErrorRecoveryMode($recognizer))
        {
            return;
        }

        $this->beginErrorCondition($recognizer);

        $t         = $recognizer->getCurrentToken();
        $tokenName = $this->getTokenErrorDisplay($t);
        $expecting = $this->getExpectedTokens($recognizer);

        $msg = "extraneous input $tokenName expecting ".self::getExpected($expecting, $recognizer->getVocabulary());

        throw new ExceptionSyntaxError($msg, ($t?->getLine() ?? -1), ($t?->getCharPositionInLine() ?? -1));
    }
    
    protected function reportMissingToken(Parser $recognizer): void
    {
        if ($this->inErrorRecoveryMode($recognizer)) {
            return;
        }

        $this->beginErrorCondition($recognizer);

        $t         = $recognizer->getCurrentToken();
        $expecting = $this->getExpectedTokens($recognizer);

        $msg = sprintf(
            'missing %s at %s',
            self::getExpected($expecting, $recognizer->getVocabulary()),
            $this->getTokenErrorDisplay($t)
        );

        throw new ExceptionSyntaxError($msg, ($t?->getLine() ?? -1), ($t?->getCharPositionInLine() ?? -1));
    }

    protected function reportNoViableAlternative(Parser $recognizer, NoViableAltException $e): void
    {
        $tokens          = $recognizer->getTokenStream();
        $offending_token = $recognizer->getCurrentToken();

        if ($tokens !== null)
        {
            $startToken = $e->getStartToken();

            if ($startToken === null)
            {
                throw new \LogicException('unexpected null start token');
            }

            if ($startToken->getType() === Token::EOF)
            {
                $msg = 'unexpected end of expression';
            }
            else
            {
                $input  = $tokens->getTextByTokens($e->getStartToken(), $e->getOffendingToken());
                $unexpr = ($offending_token?->getText() ?? '<unknown input>');
                $msg    = "unexpected input '$unexpr' at input '$input'";
            }
        }
        else
        {
            $msg = "unexpected input '<unknown input>' at input '<unknown input>'";
        }

        throw new ExceptionSyntaxError(
            $msg,
            ($offending_token?->getLine() ?? -1),
            (($offending_token?->getCharPositionInLine() ?? -2) + 1)
        );
    }
}
