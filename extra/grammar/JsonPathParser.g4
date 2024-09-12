parser grammar JsonPathParser;

options {
    tokenVocab = JsonPathLexer;
}



//======================================================================================================================
// Advanced query
query: rootQuery EOF;

/* Queries */
rootQuery: OP_ROOT (S* segment)*;
relQuery: OP_CURRENT (S* segment)*;
filterQuery
    : relQuery
    | rootQuery
;

absSingularQuery: OP_ROOT (S* singularSegment)*;
relSingularQuery: OP_CURRENT (S* singularSegment)*;
singularQuery
    : relSingularQuery
    | absSingularQuery
;



/* Expressions */
logicalAndExpression: basicExpression (S* OP_AND S* basicExpression)*;
logicalOrExpression: logicalAndExpression (S* OP_OR S* logicalAndExpression)*;

testExpression: (OP_NOT S)? (filterQuery | functionExpression);
comparisonExpression: left=comparable S* OP_COMP S* right=comparable;

parenExpression: (OP_NOT S*)? OP_PAREN_OPEN S* logicalExpression S* OP_PAREN_CLOSE;
basicExpression
    : parenExpression
    | comparisonExpression
    | testExpression
;

logicalExpression: logicalOrExpression;
functionExpression: functionName OP_PAREN_OPEN S* (functionArgument (S* OP_COMMA S* functionArgument)*)? S* OP_PAREN_CLOSE;



/* Selectors */
nameSelector: STRING;
wildcardSelector: OP_WILDCARD;
sliceSelector: (start=INT S*)? OP_SLICE S* (end=INT S*)? (OP_SLICE (S* step=INT)?)?;
indexSelector: INT;
filterSelector: OP_FILTER S* logicalExpression;

selector
    : nameSelector
    | wildcardSelector
    | sliceSelector
    | indexSelector
    | filterSelector
;



/* Segments */
segment
    : childSegment
    | descendantSegment
;
childSegment
    : bracketedSelection
    | OP_PATH (wildcardSelector | memberName)
;
descendantSegment: OP_RECURSE (bracketedSelection | wildcardSelector | memberName);

singularSegment
    : nameSegment
    | indexSegment
;
nameSegment
    : OP_BRACK_OPEN nameSelector OP_BRACK_CLOSE
    | OP_PATH memberName
;
indexSegment: OP_BRACK_OPEN indexSelector OP_BRACK_CLOSE;



/* Atoms */
literal
    : INT
    | NUMBER
    | STRING
    | BOOLEAN
    | NULL
;
functionName: NAME;
memberName: NAME;



/* Operands */
comparable
    : literal
    | singularQuery
    | functionExpression
;
functionArgument
    : literal
    | filterQuery
    | functionExpression
    | logicalExpression
;



/* Misc */
bracketedSelection: OP_BRACK_OPEN S* selector (S* OP_COMMA S* selector)* S* OP_BRACK_CLOSE;
