<?php
declare(strict_types=1);
require_once '../vendor/autoload.php';

use JsonScout\JsonScout;



readonly class TestBase
{
    //==================================================================================================================
    private static function compareResults(array $arr1, array $arr2)
        : bool
    {
        if (count($arr1) !== count($arr2))
        {
            return false;
        }
        
        foreach ($arr1 as $i => $element)
        {
            if (!array_key_exists($i, $arr2))
            {
                return false;
            }
            
            $element2 = $arr2[$i];
            
            if ($element instanceof \stdClass)
            {
                if (!($element2 instanceof \stdClass) || $element != $element2)
                {
                    return false;
                }
            }
            else if (is_array($element))
            {
                if (!self::compareResults($element, $element2))
                {
                    return false;
                }
            }
            else if ($element !== $element2)
            {
                return false;
            }
        }
        
        return true;
    }

    //==================================================================================================================
    /**
     * @param array<non-empty-string,array{'data':string|stdClass|array<mixed>, 'expect':array<mixed>, 'order'?:false}> $testCases 
     * @param array<non-empty-string,array<mixed>> $dataTable
     */
    public function __construct(
        public array $testCases,
        public array $dataTable
    ) {}

    //==================================================================================================================
    public function runTests()
        : void
    {
        $fail_count = 0;
        $time_start = microtime(true);

        foreach ($this->testCases as $query => $data)
        {
            $json_data = (is_string($data['data']) ? $this->dataTable[$data['data']] : $data['data']);
            $expected  = $data['expect'];
            $result    = JsonScout::query($query, $json_data)->toArray();
            
            if (isset($data['order']) && $data['order'] === false)
            {
                @sort($result);
                @sort($expected);
            }
            
            if (!self::compareResults($result, $expected))
            {
                echo "Failed query '$query':\n";
                echo "Got: "      . var_export($result,   true) . "\n";
                echo "Expected: " . var_export($expected, true) . "\n\n";
                
                ++$fail_count;
            }
        }

        $time_end  = microtime(true);
        $time_diff = ($time_end - $time_start);

        echo "Tests finished!\nFailed $fail_count out of " . count($this->testCases) . " tests! (in $time_diff seconds)";
    }
}