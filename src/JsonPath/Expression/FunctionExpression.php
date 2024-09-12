<?php

namespace JsonScout\JsonPath\Function\JsonPath\Expression;

use JsonScout\JsonPath\Function\JsonPath\Function\FunctionExtension;
use JsonScout\JsonPath\Function\JsonPath\Object\LogicalType;
use JsonScout\JsonPath\Function\JsonPath\Object\Node;
use JsonScout\JsonPath\Function\JsonPath\Object\NodesType;
use JsonScout\JsonPath\Function\JsonPath\Object\ValueType;



final readonly class FunctionExpression
    implements ITestable,
               IComparable,
               IFunctionParameter
{
    //==================================================================================================================
    /**
     * @param array<IFunctionParameter> $arguments
     */
    public function __construct(
        public  FunctionExtension $extension,
        private array             $arguments
    ) {}

    //==================================================================================================================
    private function invoke(Node $root, Node $current)
        : ValueType|LogicalType|NodesType
    {
        $args = [];

        foreach ($this->arguments as $i => $argument)
        {
            $class  = $this->getExpectedClassForArgument($i);
            $args[] = $argument->toParameter($class, $root, $current);
        }

        return $this->extension->invoke(...$args);
    }

    //==================================================================================================================
    #[\Override]
    public function test(Node $root, Node $current)
        : LogicalType
    {
        $result = $this->invoke($root, $current);
        assert($result instanceof NodesType || $result instanceof LogicalType);

        if ($result instanceof NodesType)
        {
            return LogicalType::fromBool(count($result->nodes) > 0);
        }

        return $result;
    }

    #[\Override]
    public function toComparable(Node $root, Node $current)
        : ValueType
    {
        $result = $this->invoke($root, $current);
        assert($result instanceof ValueType);

        return $result;
    }

    #[\Override]
    public function toParameter(string $paramaterType, Node $root, Node $current)
        : LogicalType|ValueType|NodesType
    {
        $this_type = $this->extension->returnType->getName();

        if ($paramaterType === $this->e)
        {
            
        }
    }

    //==================================================================================================================
    /**
     * @return class-string
     */
    private function getExpectedClassForArgument(int $index)
        : string
    {
        $ext_params = $this->extension->parameters;

        $param = $ext_params[min($index, (count($ext_params) - 1))];
        assert($param->getType() instanceof \ReflectionNamedType);

        /** @var class-string */
        return $param->getName();
    }
}
