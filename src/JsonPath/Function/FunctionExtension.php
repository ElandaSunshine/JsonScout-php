<?php

namespace JsonScout\JsonPath\Function\JsonPath\Function;

use JsonScout\JsonPath\Function\JsonPath\Object\LogicalType;
use JsonScout\JsonPath\Function\JsonPath\Object\NodesType;
use JsonScout\JsonPath\Function\JsonPath\Object\ValueType;
use Technically\CallableReflection\CallableReflection;



/**
 * @phpstan-type FilterValue LogicalType|NodesType|ValueType
 * @phpstan-type FunctionExtensionCallable pure-callable(FilterValue...):FilterValue
 */
final readonly class FunctionExtension
{
    //==================================================================================================================
    /** Inside function extension arguments. */
    public const int CONTEXT_ARGUMENT = 1;

    /** Inside comparison operations. */
    public const int CONTEXT_COMPARISON = 2;

    /** Inside test operations. */
    public const int CONTEXT_TEST = 4;

    //==================================================================================================================
    private static function validateType(string $element, string $extName, ?\ReflectionType $type)
        : void
    {        
        if ($type === null)
        {
            $error = "invalid $element for function extension '$extName', "
                    ."must have an explicitly specified type of either ValueType, LogicalType or NodesType";
        }
        else if ($type instanceof \ReflectionIntersectionType || $type instanceof \ReflectionUnionType)
        {
            $error = "invalid $element for function extension '$extName', "
                    ."union and intersection types are not supported";
        }
        else if (!in_array($type->getName(), [ ValueType::class, LogicalType::class, NodesType::class ], true))
        {
            $error = "invalid $element for function extension '$extName', "
                    ."found type '{$type->getName()}' but expected either ValueType, LogicalType or NodesType";
        }
        else
        {
            return;
        }
        
        throw new ExceptionFunctionRegistration($error);
    }

    /**
     * @param FunctionExtensionCallable $callable
     * @throws \ReflectionException
     */
    private static function getReflector(callable $callable)
        : \ReflectionFunctionAbstract
    {
        $reflection = CallableReflection::fromCallable($callable);
        $refl       = new \ReflectionObject($reflection);

        $prop   = $refl->getProperty('reflector');
        $result = $prop->getValue($reflection);
        assert($result instanceof \ReflectionFunctionAbstract);

        return $result;
    }

    //==================================================================================================================
    /** @var FunctionExtensionCallable */
    private mixed $callable;
    private int   $applicableContexts;
    
    /** @var \ReflectionParameter[] */
    public array $parameters;

    /** @var class-string $returnType  */
    public string $returnType;

    //==================================================================================================================
    /** 
     * @param non-empty-string $extensionName
     * @param FunctionExtensionCallable $callable
     * 
     * @param-later-invoked-callable $callable
     */
    public function __construct(
        public string   $extensionName,
               callable $callable
    )
    {
        if (preg_match('/[a-z][a-z0-9_]*/', $extensionName) === false)
        {
            throw new ExceptionFunctionRegistration(
                "invalid function extension name '$extensionName', must start with a lower-case letter and can only "
                ."contain lower-case letters, digits und underscores"
            );
        }

        try {
            $refl = self::getReflector($callable);
        }
        catch (\Exception $ex)
        {
            throw new ExceptionFunctionRegistration($ex->getMessage());
        }

        $pars = [];

        foreach ($refl->getParameters() as $parameter)
        {
            self::validateType("parameter '{$parameter->getName()}'", $extensionName, $parameter->getType());
            $pars[] = $parameter;
        }

        $type = $refl->getReturnType();
        self::validateType("return type", $extensionName, $type);

        $name     = $type?->getName();
        $contexts = 0b0111;

        if (in_array($name, [ NodesType::class, LogicalType::class ], true))
        {
            $contexts &= ~self::CONTEXT_COMPARISON;
        }
        else if ($name === ValueType::class)
        {
            $contexts &= ~self::CONTEXT_TEST;
        }

        $this->callable           = $callable;
        $this->applicableContexts = $contexts;
        $this->returnType         = $type;
        $this->parameters         = $pars;
    }

    //==================================================================================================================
    public function canBeUsedFor(int $flag) : bool { return (($this->applicableContexts & $flag) == $flag); }

    //==================================================================================================================
    public function invoke(ValueType|NodesType|LogicalType ...$arguments)
        : ValueType|LogicalType|NodesType
    {
        return ($this->callable)(...$arguments);
    }
}
