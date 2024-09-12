<?php

namespace JsonScout\JsonPath\Function\JsonPath\Parser;

use Antlr\Antlr4\Runtime\Error\DefaultErrorStrategy;
use Antlr\Antlr4\Runtime\Error\Exceptions\InputMismatchException;
use Antlr\Antlr4\Runtime\IntervalSet;
use Antlr\Antlr4\Runtime\Parser;
use Antlr\Antlr4\Runtime\Vocabulary;

class JsonPathErrorStrategy
    extends DefaultErrorStrategy
{
    //==================================================================================================================
    private static function getExpectedString(IntervalSet $expectedTokens, Vocabulary $vocab)
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
            throw new \LogicException('Unexpected null expected tokens.');
        }
        
        $offending_token = $e->getOffendingToken();

        $msg = sprintf(
            'mismatched input %s expecting %s',
            $this->getTokenErrorDisplay($offending_token),
            self::getExpectedString($expected_tokens, $recognizer->getVocabulary())
        );

        throw new ExceptionSyntaxError($msg, $offending_token->getLine(), $offending_token->getCharPositionInLine());
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

        $t = $recognizer->getCurrentToken();
        $tokenName = $this->getTokenErrorDisplay($t);
        $expecting = $this->getExpectedTokens($recognizer);

        $msg = \sprintf(
            'extraneous input %s expecting %s',
            $tokenName,
            self::getExpectedString($expecting, $recognizer->getVocabulary())
        );

        throw new ExceptionSyntaxError($msg, $t->getLine(), $t->getCharPositionInLine());
    }
    
    protected function reportMissingToken(Parser $recognizer): void
    {
        if ($this->inErrorRecoveryMode($recognizer)) {
            return;
        }

        $this->beginErrorCondition($recognizer);

        $t = $recognizer->getCurrentToken();
        $expecting = $this->getExpectedTokens($recognizer);

        $msg = \sprintf(
            'missing %s at %s',
            self::getExpectedString($expecting, $recognizer->getVocabulary()),
            $this->getTokenErrorDisplay($t)
        );

        throw new ExceptionSyntaxError($msg, $t->getLine(), $t->getCharPositionInLine());
    }
}
