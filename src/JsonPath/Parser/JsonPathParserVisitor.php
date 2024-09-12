<?php

/*
 * Generated from d:/Development/Coding/antlr4/JsonPath/JsonPathParser.g4 by ANTLR 4.13.1
 */

namespace JsonScout\JsonPath\Function\JsonPath\Parser;

use Antlr\Antlr4\Runtime\Tree\ParseTreeVisitor;

/**
 * This interface defines a complete generic visitor for a parse tree produced by {@see JsonPathParser}.
 */
interface JsonPathParserVisitor extends ParseTreeVisitor
{
    /**
     * Visit a parse tree produced by {@see JsonPathParser::query()}.
     *
     * @param Context\QueryContext $context The parse tree.
     *
     * @return mixed The visitor result.
     */
    public function visitQuery(Context\QueryContext $context);

    /**
     * Visit a parse tree produced by {@see JsonPathParser::rootQuery()}.
     *
     * @param Context\RootQueryContext $context The parse tree.
     *
     * @return mixed The visitor result.
     */
    public function visitRootQuery(Context\RootQueryContext $context);

    /**
     * Visit a parse tree produced by {@see JsonPathParser::relQuery()}.
     *
     * @param Context\RelQueryContext $context The parse tree.
     *
     * @return mixed The visitor result.
     */
    public function visitRelQuery(Context\RelQueryContext $context);

    /**
     * Visit a parse tree produced by {@see JsonPathParser::filterQuery()}.
     *
     * @param Context\FilterQueryContext $context The parse tree.
     *
     * @return mixed The visitor result.
     */
    public function visitFilterQuery(Context\FilterQueryContext $context);

    /**
     * Visit a parse tree produced by {@see JsonPathParser::absSingularQuery()}.
     *
     * @param Context\AbsSingularQueryContext $context The parse tree.
     *
     * @return mixed The visitor result.
     */
    public function visitAbsSingularQuery(Context\AbsSingularQueryContext $context);

    /**
     * Visit a parse tree produced by {@see JsonPathParser::relSingularQuery()}.
     *
     * @param Context\RelSingularQueryContext $context The parse tree.
     *
     * @return mixed The visitor result.
     */
    public function visitRelSingularQuery(Context\RelSingularQueryContext $context);

    /**
     * Visit a parse tree produced by {@see JsonPathParser::singularQuery()}.
     *
     * @param Context\SingularQueryContext $context The parse tree.
     *
     * @return mixed The visitor result.
     */
    public function visitSingularQuery(Context\SingularQueryContext $context);

    /**
     * Visit a parse tree produced by {@see JsonPathParser::logicalAndExpression()}.
     *
     * @param Context\LogicalAndExpressionContext $context The parse tree.
     *
     * @return mixed The visitor result.
     */
    public function visitLogicalAndExpression(Context\LogicalAndExpressionContext $context);

    /**
     * Visit a parse tree produced by {@see JsonPathParser::logicalOrExpression()}.
     *
     * @param Context\LogicalOrExpressionContext $context The parse tree.
     *
     * @return mixed The visitor result.
     */
    public function visitLogicalOrExpression(Context\LogicalOrExpressionContext $context);

    /**
     * Visit a parse tree produced by {@see JsonPathParser::testExpression()}.
     *
     * @param Context\TestExpressionContext $context The parse tree.
     *
     * @return mixed The visitor result.
     */
    public function visitTestExpression(Context\TestExpressionContext $context);

    /**
     * Visit a parse tree produced by {@see JsonPathParser::comparisonExpression()}.
     *
     * @param Context\ComparisonExpressionContext $context The parse tree.
     *
     * @return mixed The visitor result.
     */
    public function visitComparisonExpression(Context\ComparisonExpressionContext $context);

    /**
     * Visit a parse tree produced by {@see JsonPathParser::parenExpression()}.
     *
     * @param Context\ParenExpressionContext $context The parse tree.
     *
     * @return mixed The visitor result.
     */
    public function visitParenExpression(Context\ParenExpressionContext $context);

    /**
     * Visit a parse tree produced by {@see JsonPathParser::basicExpression()}.
     *
     * @param Context\BasicExpressionContext $context The parse tree.
     *
     * @return mixed The visitor result.
     */
    public function visitBasicExpression(Context\BasicExpressionContext $context);

    /**
     * Visit a parse tree produced by {@see JsonPathParser::logicalExpression()}.
     *
     * @param Context\LogicalExpressionContext $context The parse tree.
     *
     * @return mixed The visitor result.
     */
    public function visitLogicalExpression(Context\LogicalExpressionContext $context);

    /**
     * Visit a parse tree produced by {@see JsonPathParser::functionExpression()}.
     *
     * @param Context\FunctionExpressionContext $context The parse tree.
     *
     * @return mixed The visitor result.
     */
    public function visitFunctionExpression(Context\FunctionExpressionContext $context);

    /**
     * Visit a parse tree produced by {@see JsonPathParser::nameSelector()}.
     *
     * @param Context\NameSelectorContext $context The parse tree.
     *
     * @return mixed The visitor result.
     */
    public function visitNameSelector(Context\NameSelectorContext $context);

    /**
     * Visit a parse tree produced by {@see JsonPathParser::wildcardSelector()}.
     *
     * @param Context\WildcardSelectorContext $context The parse tree.
     *
     * @return mixed The visitor result.
     */
    public function visitWildcardSelector(Context\WildcardSelectorContext $context);

    /**
     * Visit a parse tree produced by {@see JsonPathParser::sliceSelector()}.
     *
     * @param Context\SliceSelectorContext $context The parse tree.
     *
     * @return mixed The visitor result.
     */
    public function visitSliceSelector(Context\SliceSelectorContext $context);

    /**
     * Visit a parse tree produced by {@see JsonPathParser::indexSelector()}.
     *
     * @param Context\IndexSelectorContext $context The parse tree.
     *
     * @return mixed The visitor result.
     */
    public function visitIndexSelector(Context\IndexSelectorContext $context);

    /**
     * Visit a parse tree produced by {@see JsonPathParser::filterSelector()}.
     *
     * @param Context\FilterSelectorContext $context The parse tree.
     *
     * @return mixed The visitor result.
     */
    public function visitFilterSelector(Context\FilterSelectorContext $context);

    /**
     * Visit a parse tree produced by {@see JsonPathParser::selector()}.
     *
     * @param Context\SelectorContext $context The parse tree.
     *
     * @return mixed The visitor result.
     */
    public function visitSelector(Context\SelectorContext $context);

    /**
     * Visit a parse tree produced by {@see JsonPathParser::segment()}.
     *
     * @param Context\SegmentContext $context The parse tree.
     *
     * @return mixed The visitor result.
     */
    public function visitSegment(Context\SegmentContext $context);

    /**
     * Visit a parse tree produced by {@see JsonPathParser::childSegment()}.
     *
     * @param Context\ChildSegmentContext $context The parse tree.
     *
     * @return mixed The visitor result.
     */
    public function visitChildSegment(Context\ChildSegmentContext $context);

    /**
     * Visit a parse tree produced by {@see JsonPathParser::descendantSegment()}.
     *
     * @param Context\DescendantSegmentContext $context The parse tree.
     *
     * @return mixed The visitor result.
     */
    public function visitDescendantSegment(Context\DescendantSegmentContext $context);

    /**
     * Visit a parse tree produced by {@see JsonPathParser::singularSegment()}.
     *
     * @param Context\SingularSegmentContext $context The parse tree.
     *
     * @return mixed The visitor result.
     */
    public function visitSingularSegment(Context\SingularSegmentContext $context);

    /**
     * Visit a parse tree produced by {@see JsonPathParser::nameSegment()}.
     *
     * @param Context\NameSegmentContext $context The parse tree.
     *
     * @return mixed The visitor result.
     */
    public function visitNameSegment(Context\NameSegmentContext $context);

    /**
     * Visit a parse tree produced by {@see JsonPathParser::indexSegment()}.
     *
     * @param Context\IndexSegmentContext $context The parse tree.
     *
     * @return mixed The visitor result.
     */
    public function visitIndexSegment(Context\IndexSegmentContext $context);

    /**
     * Visit a parse tree produced by {@see JsonPathParser::literal()}.
     *
     * @param Context\LiteralContext $context The parse tree.
     *
     * @return mixed The visitor result.
     */
    public function visitLiteral(Context\LiteralContext $context);

    /**
     * Visit a parse tree produced by {@see JsonPathParser::functionName()}.
     *
     * @param Context\FunctionNameContext $context The parse tree.
     *
     * @return mixed The visitor result.
     */
    public function visitFunctionName(Context\FunctionNameContext $context);

    /**
     * Visit a parse tree produced by {@see JsonPathParser::memberName()}.
     *
     * @param Context\MemberNameContext $context The parse tree.
     *
     * @return mixed The visitor result.
     */
    public function visitMemberName(Context\MemberNameContext $context);

    /**
     * Visit a parse tree produced by {@see JsonPathParser::comparable()}.
     *
     * @param Context\ComparableContext $context The parse tree.
     *
     * @return mixed The visitor result.
     */
    public function visitComparable(Context\ComparableContext $context);

    /**
     * Visit a parse tree produced by {@see JsonPathParser::functionArgument()}.
     *
     * @param Context\FunctionArgumentContext $context The parse tree.
     *
     * @return mixed The visitor result.
     */
    public function visitFunctionArgument(Context\FunctionArgumentContext $context);

    /**
     * Visit a parse tree produced by {@see JsonPathParser::bracketedSelection()}.
     *
     * @param Context\BracketedSelectionContext $context The parse tree.
     *
     * @return mixed The visitor result.
     */
    public function visitBracketedSelection(Context\BracketedSelectionContext $context);
}