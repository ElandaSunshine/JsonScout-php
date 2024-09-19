<?php

namespace JsonScout;



final class Config
{
    //==================================================================================================================
    public const string OPTIMISATIONS = 'optimisation';
    
    //------------------------------------------------------------------------------------------------------------------
    private static ?self $INSTANCE = null;
    
    //==================================================================================================================
    public static function getInstance()
        : self
    {
        if (self::$INSTANCE === null)
        {
            self::$INSTANCE = new self;
        }

        return self::$INSTANCE;
    }
    
    //==================================================================================================================
    /**
     * @var array<string,int|float|string|bool|null>
     */
    private readonly array $data;
    
    //==================================================================================================================
    public function __construct()
    {
        /** @var array<string,int|float|string|bool|null>|null */
        $data = json_decode(__DIR__ . '/../config.json', true);
        
        if ($data === null)
        {
            throw new \RuntimeException("problem reading JsonScout config");
        }
        
        $this->data = $data;
    }
    
    //==================================================================================================================
    public function getProperty(string $category, string $property)
        : bool|int|float|string|null
    {
        if (!isset($this->data[$category][$property]))
        {
            return null;
        }
        
        return $this->data[$category][$property];
    }
}
