<?php

namespace JsonScout\JsonPath\Function\JsonPath\Parser;

use Antlr\Antlr4\Runtime\Tree\AbstractParseTreeVisitor;
use JsonScout\JsonPath\Function\ExceptionFunctionExtension;
use JsonScout\JsonPath\Expression;
use JsonScout\JsonPath\Function\JsonPath\Function\FunctionExtension;
use JsonScout\JsonPath\Function\JsonPath\Function\FunctionRegistry;
use JsonScout\JsonPath\Function\JsonPath\Object\LogicalType;
use JsonScout\JsonPath\Function\JsonPath\Object\NodesType;
use JsonScout\JsonPath\Function\JsonPath\Object\ValueType;
use JsonScout\JsonPath\Function\JsonPath\Parser\Context\FunctionArgumentContext;



/**
 * @extends AbstractParseTreeVisitor<mixed>
 */
class JsonPathVisitor
	extends AbstractParseTreeVisitor
	implements JsonPathParserVisitor
{
#region helpers
    /** @param FunctionArgumentContext[] $array */
    private static function popFront(array &$array)
        : FunctionArgumentContext|false
    {
        $result = reset($array);

        if ($result !== false)
        {
            unset($array[key($array)]);
        }

        return $result;
    }

    /** @param class-string $class */
    private static function getExpected(string $class)
        : string
    {
        if ($class === LogicalType::class)
        {
            return "either ".LogicalType::class." or ".NodesType::class;
        }

        return $class;
    }

    //==================================================================================================================
    /**
     * @param Context\SegmentContext[]|Context\SingularSegmentContext[] $segmentArray
     */
	private function queryFromSegments(array $segmentArray, bool $relative, bool|null $singular)
		: Expression\QueryExpression
	{
		$segments = [];
		
		foreach ($segmentArray as $segment_ctx)
		{
			$segment = $this->visit($segment_ctx);
			assert($segment instanceof Expression\SegmentExpression);
			
			$segments[] = $segment;
		}

        if ($singular === null)
        {
            foreach ($segments as $segment)
            {
                if (!$segment->singular)
                {
                    $singular = false;
                    break;
                }
            }
        }

		return new Expression\QueryExpression($segments, $relative, ($singular ?? true));
	}

    /**
     * @param Context\BracketedSelectionContext|Context\WildcardSelectorContext|Context\MemberNameContext $context
     */
    private function segmentFromSelectors(mixed $context, bool $recursive)
        : Expression\SegmentExpression
    {
        if ($context instanceof Context\BracketedSelectionContext)
        {
            $result = $this->visit($context);
            assert(is_array($result));
            return new Expression\SegmentExpression($result, $recursive);
        }

        $result = $this->visit($context);
        assert($result instanceof Expression\Selector\ISegmentSelector);

        return new Expression\SegmentExpression([ $result ], $recursive);
    }

    /**
     * @param Context\LogicalAndExpressionContext[]|Context\BasicExpressionContext[] $children
     */
    private function logicalExpressionFromChildren(array $children, Expression\LogicalOperation $opType)
        : Expression\AbstractLogicalExpression
    {
        $result = [];

        foreach ($children as $expr)
        {
            $operation = $this->visit($expr);
            assert($operation instanceof Expression\AbstractLogicalExpression);
            $result[] = $operation;
        }

        if (count($result) === 1)
        {
            return $result[0];
        }

        return new Expression\LogicalExpression($opType, $result);
    }

    private function consumeArgument(\ReflectionParameter            $parameter,
                                     Context\FunctionArgumentContext $ctx,
                                     FunctionExtension               $ext,
                                     int                             $index)
        : Expression\IFunctionParameter
    {
        $argument = $this->visit($ctx);
        assert($argument instanceof Expression\IFunctionParameter);

        assert($parameter->getType() instanceof \ReflectionNamedType);

        /** @var class-string $type_name */
        $type_name = $parameter->getType()->getName();
        $error     = "";

        if (!$argument->validateParameter($type_name, $error))
        {
            throw new ExceptionFunctionExtension(
                "invalid function call to '$ext->extensionName' at parameter #$index ({$parameter->getName()}), ".$error
            );
        }

        return $argument;
    }
#endregion helpers
#region query
	#[\Override]
	public function visitQuery(Context\QueryContext $context)
		: JsonPathQuery
	{
		assert($context->rootQuery() !== null);
		
		$result = $this->visit($context->rootQuery());
		assert($result instanceof Expression\QueryExpression);
		
		return new JsonPathQuery($result);
	}

	#[\Override]
	public function visitRootQuery(Context\RootQueryContext $context)
		: Expression\QueryExpression
	{
        $segments = $context->segment();
        assert(is_array($segments));
		return $this->queryFromSegments($segments, false, null);
	}
	
	#[\Override]
	public function visitRelQuery(Context\RelQueryContext $context)
        : Expression\QueryExpression
	{
        $segments = $context->segment();
        assert(is_array($segments));
        return $this->queryFromSegments($segments, true, null);
	}

    #[\Override]
    public function visitAbsSingularQuery(Context\AbsSingularQueryContext $context)
        : Expression\QueryExpression
    {
        $segments = $context->singularSegment();
        assert(is_array($segments));
        return $this->queryFromSegments($segments, false, true);
    }

	#[\Override]
	public function visitRelSingularQuery(Context\RelSingularQueryContext $context)
        : Expression\QueryExpression
	{
        $segments = $context->singularSegment();
        assert(is_array($segments));
        return $this->queryFromSegments($segments, true, true);
	}

    #[\Override]
    public function visitFilterQuery(Context\FilterQueryContext $context)
        : Expression\QueryExpression
    {
        $actor = ($context->relQuery() ?? $context->rootQuery());
        assert($actor !== null);

        $result = $this->visit($actor);
        assert($result instanceof Expression\QueryExpression);

        return $result;
    }

	#[\Override]
	public function visitSingularQuery(Context\SingularQueryContext $context)
        : Expression\QueryExpression
	{
		$actor = ($context->relSingularQuery() ?? $context->absSingularQuery());
		assert($actor !== null);
		
		$result = $this->visit($actor);
		assert($result instanceof Expression\QueryExpression);
		
		return $result;
	}
#endregion query
#region selector
	#[\Override]
	public function visitNameSelector(Context\NameSelectorContext $context)
		: Expression\Selector\ChildSelector
	{
        $string = $context->STRING()?->getText();
		assert($string !== null);
		return new Expression\Selector\ChildSelector(substr($string, 1, -1));
	}

	#[\Override]
	public function visitWildcardSelector(Context\WildcardSelectorContext $context)
		: Expression\Selector\WildcardSelector
	{
		return new Expression\Selector\WildcardSelector();
	}

	#[\Override]
	public function visitSliceSelector(Context\SliceSelectorContext $context)
		: Expression\Selector\SliceSelector
	{
		$start = (isset($context->start) ? (int) $context->start->getText() : null);
		$end   = (isset($context->end)   ? (int) $context->end  ->getText() : null);
		$step  = (isset($context->step)  ? (int) $context->step ->getText() : 1);
	    return new Expression\Selector\SliceSelector($start, $end, $step);
	}

	#[\Override]
	public function visitIndexSelector(Context\IndexSelectorContext $context)
        : Expression\Selector\ChildSelector
	{
        $string = $context->INT()?->getText();
		assert($string !== null);
		return new Expression\Selector\ChildSelector((int) $string);
	}
		
	#[\Override]
	public function visitFilterSelector(Context\FilterSelectorContext $context)
		: Expression\Selector\FilterSelector
	{
		assert($context->logicalExpression() !== null);

		$filter = $this->visit($context->logicalExpression());
		assert($filter instanceof Expression\AbstractLogicalExpression);
		
	    return new Expression\Selector\FilterSelector($filter);
	}

	#[\Override]
	public function visitSelector(Context\SelectorContext $context)
		: Expression\Selector\ISegmentSelector
	{
		$child = $context->getChild(0);
		assert($child !== null);

		$result = $child->accept($this);
		assert($result instanceof Expression\Selector\ISegmentSelector);

	    return $result;
	}

    /** @return Expression\Selector\ISegmentSelector[] */
    #[\Override]
    public function visitBracketedSelection(Context\BracketedSelectionContext $context)
        : array
    {
        assert(is_array($context->selector()));
        $result = [];

        foreach ($context->selector() as $selector)
        {
            $expression = $this->visit($selector);
            assert($expression instanceof Expression\Selector\ISegmentSelector);

            $result[] = $expression;
        }

        return $result;
    }
#endregion selector
#region segment
	#[\Override]
	public function visitDescendantSegment(Context\DescendantSegmentContext $context)
		: Expression\SegmentExpression
	{
        $actor = ($context->memberName() ?? $context->bracketedSelection() ?? $context->wildcardSelector());
        assert($actor !== null);
	    return $this->segmentFromSelectors($actor, true);
	}

	#[\Override]
	public function visitChildSegment(Context\ChildSegmentContext $context)
		: Expression\SegmentExpression
	{
        $actor = ($context->memberName() ?? $context->bracketedSelection() ?? $context->wildcardSelector());
        assert($actor !== null);
        return $this->segmentFromSelectors($actor, false);
	}

	#[\Override]
	public function visitSegment(Context\SegmentContext $context)
		: Expression\SegmentExpression
	{
        $actor = ($context->childSegment() ?? $context->descendantSegment());
		assert($actor !== null);

		$result = $this->visit($actor);
		assert($result instanceof Expression\SegmentExpression);

		return $result;
	}
	
	#[\Override]
	public function visitNameSegment(Context\NameSegmentContext $context)
		: Expression\Selector\ChildSelector
	{
        $actor = ($context->memberName() ?? $context->nameSelector());
		assert($actor !== null);
		
		$result = $this->visit($actor);
		assert($result instanceof Expression\Selector\ChildSelector);

		return $result;
	}

    #[\Override]
    public function visitMemberName(Context\MemberNameContext $context)
        : Expression\Selector\ChildSelector
    {
        $name = $context->NAME()?->getText();
        assert($name !== null);
        return new Expression\Selector\ChildSelector($name);
    }
		
	#[\Override]
	public function visitIndexSegment(Context\IndexSegmentContext $context)
		: Expression\Selector\ChildSelector
	{
	    assert($context->indexSelector() !== null);

		$result = $this->visit($context->indexSelector());
		assert($result instanceof Expression\Selector\ChildSelector);

		return $result;
	}
	
	#[\Override]
	public function visitSingularSegment(Context\SingularSegmentContext $context)
		: Expression\SegmentExpression
	{
		$actor = ($context->nameSegment() ?? $context->indexSegment());
	    assert($actor !== null);

		$result = $this->visit($actor);
		assert($result instanceof Expression\Selector\ChildSelector);

		return new Expression\SegmentExpression([ $result ], false);
	}
#endregion segment
#region filter
	#[\Override]
	public function visitTestExpression(Context\TestExpressionContext $context)
		: Expression\TestExpression
	{
		$actor = ($context->filterQuery() ?? $context->functionExpression());
		assert($actor !== null);
		
		$result = $this->visit($actor);
		assert($result instanceof Expression\ITestable);
		
		if ($context->functionExpression() !== null)
		{
			assert($result instanceof Expression\FunctionExpression);
			$extension = $result->extension;

			if (!$extension->canBeUsedFor(FunctionExtension::CONTEXT_TEST))
			{
				throw new ExceptionFunctionExtension(
					"function extension '{$extension->extensionName}' can not be used in a test expression, returns "
					."'{$extension->returnType}' but expected either ".LogicalType::class." or ".NodesType::class
				);
			}
		}

	    return new Expression\TestExpression($result, ($context->OP_NOT() !== null));
	}

	#[\Override]
	public function visitComparisonExpression(Context\ComparisonExpressionContext $context)
		: Expression\ComparisonExpression
	{
		assert($context->left !== null && $context->right !== null);
		
		$left  = $this->visit($context->left);
		$right = $this->visit($context->right);

        assert($left instanceof Expression\IComparable && $right instanceof Expression\IComparable);

        $op = $context->OP_COMP()?->getText();
        assert($op != null);

		return new Expression\ComparisonExpression(Expression\ComparisonOperation::fromString($op), $left, $right);
	}

	#[\Override]
	public function visitParenExpression(Context\ParenExpressionContext $context)
		: Expression\AbstractLogicalExpression
	{
        $actor = $context->logicalExpression();
        assert($actor !== null);

		$result = $this->visit($actor);
		assert($result instanceof Expression\AbstractLogicalExpression);
		
	    return ($context->OP_NOT() !== null
            ? new Expression\NegationExpression($result)
            : $result);
	}

	#[\Override]
	public function visitLogicalAndExpression(Context\LogicalAndExpressionContext $context)
		: Expression\AbstractLogicalExpression
	{
        $actor = $context->basicExpression();
		assert(is_array($actor));

		return $this->logicalExpressionFromChildren($actor, Expression\LogicalOperation::And);
	}

	#[\Override]
	public function visitLogicalOrExpression(Context\LogicalOrExpressionContext $context)
		: Expression\AbstractLogicalExpression
	{
        $actor = $context->logicalAndExpression();
        assert(is_array($actor));

        return $this->logicalExpressionFromChildren($actor, Expression\LogicalOperation::Or);
	}

	#[\Override]
	public function visitLogicalExpression(Context\LogicalExpressionContext $context)
		: Expression\AbstractLogicalExpression
	{
        $actor = $context->logicalOrExpression();
		assert($actor !== null);

		$op = $this->visit($actor);
		assert($op instanceof Expression\AbstractLogicalExpression);
		
	    return $op;
	}

	#[\Override]
	public function visitBasicExpression(Context\BasicExpressionContext $context)
		: Expression\AbstractLogicalExpression
	{
		$child = $context->getChild(0);
		assert($child !== null);
		
		$result = $child->accept($this);
		assert($result instanceof Expression\AbstractLogicalExpression);
		
	    return $result;
	}
	
	#[\Override]
	public function visitLiteral(Context\LiteralContext $context)
		: Expression\Literal
	{
		if ($context->NUMBER() !== null || $context->INT() !== null)
		{
			$number_string = ($context->NUMBER() ?? $context->INT())?->getText();
			assert($number_string !== null);

			if (preg_match('/[eE.]/', $number_string) === 1)
			{
				$result = (float) $number_string;
			}
			else
			{
				$result = (int) $number_string;	
			}
		}
		else if ($context->STRING() !== null)
		{
			$string = $context->STRING()->getText();
			assert($string !== null);

			$result = substr($string, 1, -1);
		}
		else if ($context->BOOLEAN() !== null)
		{
			$boolean = $context->BOOLEAN()->getText();
			assert($boolean === 'false' || $boolean === 'true');
			
			$result = ($boolean === 'true');
		}
		else if ($context->NULL() !== null)
		{
			assert($context->NULL()->getText() === 'null');
			$result = null;
		}
		else
		{
			throw new ExceptionInternalError("literal could not be processed, got '{$context->getText()}'");
		}
		
		return new Expression\Literal(new ValueType($result));
	}

	#[\Override]
	public function visitComparable(Context\ComparableContext $context)
		: Expression\IComparable
	{
		$actor = ($context->singularQuery() ?? $context->functionExpression() ?? $context->literal());
		assert($actor !== null);
		
		$result = $this->visit($actor);
		assert($result instanceof Expression\IComparable);

        if ($context->functionExpression() !== null)
        {
            assert($result instanceof Expression\FunctionExpression);
            $extension = $result->extension;

            if (!$extension->canBeUsedFor(FunctionExtension::CONTEXT_COMPARISON))
            {
                throw new ExceptionFunctionExtension(
                    "function extension '{$extension->extensionName}' can not be used in a comparison, returns "
                    ."'{$extension->returnType}' but expected ".ValueType::class
                );
            }
        }

	    return $result;
	}
#endregion filter
#region function
    #[\Override]
    public function visitFunctionExpression(Context\FunctionExpressionContext $context)
        : Expression\FunctionExpression
    {
        $name_ctx = $context->functionName();
        assert($name_ctx !== null);

        $name = $this->visit($name_ctx);
        assert(is_string($name));

        $registry  = FunctionRegistry::getInstance();
        $extension = $registry->getExtension($name);

        if ($extension === null)
        {
            throw new ExceptionFunctionExtension("no function extension with name '$name' was registered");
        }

        $args_ctx = $context->functionArgument();
        assert(is_array($args_ctx));

        $argument_contexts = $args_ctx;
        $arguments         = [];

        foreach ($extension->parameters as $i => $parameter)
        {
            $ctx = self::popFront($argument_contexts);

            if ($parameter->isVariadic())
            {
                while ($ctx !== false)
                {
                    $arguments[] = $this->consumeArgument($parameter, $ctx, $extension, $i);
                    $ctx         = self::popFront($argument_contexts);
                }
            }
            else
            {
                if ($ctx === false)
                {
                    if (!$parameter->isDefaultValueAvailable())
                    {
                        $needs = count(
                            array_filter($extension->parameters, function($param)
                            {
                                return !$param->isDefaultValueAvailable() && !$param->isVariadic();
                            })
                        );

                        throw new ExceptionFunctionExtension(
                            "too few arguments to function extension '{$extension->extensionName}', "
                            ."expected at least $needs but received only ".count($args_ctx)
                        );
                    }

                    break;
                }

                $arguments[] = $this->consumeArgument($parameter, $ctx, $extension, $i);
            }
        }

        return new Expression\FunctionExpression($extension, $arguments);
    }

    #[\Override]
    public function visitFunctionName(Context\FunctionNameContext $context)
        : string
    {
        $name = $context->NAME()?->getText();
        assert($name !== null);

        if (preg_match("/^[a-z][a-z0-9_]*$/", $name) === false)
        {
            $token = $context->getStart();
            assert($token !== null);

            $line   = $token->getLine();
            $column = $token->getCharPositionInLine();

            throw new ExceptionSyntaxError(
                "function name '$name' contains invalid characters, must start with a lowercase letter and only "
                ."contain lowercase letters, digits and underscores",
                $line, $column
            );
        }

        return $name;
    }

    #[\Override]
    public function visitFunctionArgument(Context\FunctionArgumentContext $context)
        : Expression\IFunctionParameter
    {
        $child = $context->getChild(0);
        assert($child !== null);

        $result = $child->accept($this);
        assert($result instanceof Expression\IFunctionParameter);

        return $result;
    }
#endregion function
}
