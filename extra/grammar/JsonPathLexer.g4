lexer grammar JsonPathLexer;



fragment Digit: [0-9];
fragment Digit1: [1-9];
fragment Hexdig: [A-F0-9];
fragment Alpha: [a-zA-Z];

fragment ESC: '\\';

fragment NonSurrogate
    : ((Digit | [ABCEFabcef]) Hexdig Hexdig Hexdig)
    | ([dD] [0-7] Hexdig Hexdig);
fragment HighSurrogate: [dD] [89ABab] Hexdig Hexdig;
fragment LowSurrogate: [dD] [CDEFcdef] Hexdig Hexdig;

fragment HexChar
    : NonSurrogate
    | (HighSurrogate '\\u' LowSurrogate)
;

fragment Unescaped
    : '\u0020'..'\u0021'
    | '\u0023'..'\u0026'
    | '\u0028'..'\u005B'
    | '\u005D'..'\uD7FF'
    | '\uE000'..'\uFFFF'
;
fragment Escapable
    : [\b\f\n\r\t/\\]
    | 'u' HexChar
;

fragment Fraction: '.' Digit*;
fragment Exponent: [eE] [\-+]? Digit*;

fragment NameFirst
    : Alpha
    | '_'
    | '\u0080'..'\uD7FF'
    | '\uE000'..'\uFFFF'
;
fragment NameChar
    : Digit
    | NameFirst
;

fragment TEXT_DOUBLE_QUOTED
    : Unescaped
    | '\''
    | ESC '"'
    | ESC Escapable
;
fragment TEXT_SINGLE_QUOTED
    : Unescaped
    | '"'
    | ESC '\''
    | ESC Escapable
;



/** Literals */
STRING
    : OP_QUOTE_SINGLE TEXT_SINGLE_QUOTED* OP_QUOTE_SINGLE
    | OP_QUOTE_DOUBLE TEXT_DOUBLE_QUOTED* OP_QUOTE_DOUBLE
;
INT
    : '0'
    | ('-'? Digit1 Digit*)
;
NUMBER: (INT | '-0') Fraction? Exponent?;
BOOLEAN: 'true' | 'false';
NULL: 'null';

NAME: NameFirst NameChar*;

OP_ROOT: '$';
OP_WILDCARD: '*';
OP_SLICE: ':';
OP_FILTER: '?';
OP_NOT: '!';
OP_PATH: '.';
OP_RECURSE: '..';
OP_CURRENT: '@';
OP_COMMA: ',';
OP_OR: '||';
OP_AND: '&&';
OP_COMP: '==' | '!=' | '<=' | '>=' | '<' | '>';
OP_PAREN_OPEN: '(';
OP_PAREN_CLOSE: ')';
OP_BRACK_OPEN: '[';
OP_BRACK_CLOSE: ']';
OP_QUOTE_SINGLE: '\'';
OP_QUOTE_DOUBLE: '"';

S: (
    ' '
    | '\t'
    | '\n'
    | '\r'
) -> skip;
