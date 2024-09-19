<?php
declare(strict_types=1);

use JsonScout\JsonScout;
use PHPUnit\Framework\TestCase;



abstract class TestBase
    extends TestCase
{
    //==================================================================================================================
    private static function compareResults(array $expected, array $input, ?array &$error)
        : bool
    {
        if (count($expected) !== count($input))
        {
            $error = [
                "Expected: " . var_export($expected, true),
                "Got: " . var_export($input, true)
            ];
            return false;
        }
        
        foreach ($expected as $i => $element)
        {
            if (!array_key_exists($i, $input))
            {
                $error = [
                    "Expected: '$i' => " . var_export($element),
                    "Got: undefined",
                ];
                return false;
            }
            
            $element2 = $input[$i];
            
            if ($element instanceof \stdClass)
            {
                if (!($element2 instanceof \stdClass) || $element != $element2)
                {
                    $error = [
                        "Expected: " . var_export($element, true),
                        "Got: " . var_export($element2, true)
                    ];
                    return false;
                }
            }
            else if (is_array($element))
            {
                if (!self::compareResults($element, $element2, $error))
                {
                    return false;
                }
            }
            else if ($element !== $element2)
            {
                $error = [
                    "Expected: " . var_export($element, true),
                    "Got: " . var_export($element2, true)
                ];
                return false;
            }
        }
        
        return true;
    }

    //==================================================================================================================
    protected array $testData  = [];
    protected array $testCases = [];

    //==================================================================================================================
    public function testSuite()
        : void
    {
        $fail_count = 0;
        $time_start = microtime(true);

        foreach ($this->testCases as $query => $data)
        {
            $json_data = (is_string($data['data']) ? $this->testData[$data['data']] : $data['data']);
            $expected  = $data['expect'];
            $result    = JsonScout::query($query, $json_data)->toArray();
            
            if (isset($data['order']) && $data['order'] === false)
            {
                @sort($result);
                @sort($expected);
            }
            
            $error = null;
            $eval = self::compareResults($expected, $result, $error);

            if (isset($data['fail']) && $data['fail'] === true)
            {
                self::assertFalse($eval, "expected was equal to input");
            }
            else
            {
                self::assertTrue(
                    $eval,
                    ($error !== null ? "query '$query' has failed:\n" . $error[0] . "\n" . $error[1] : '')
                );
            }
        }

        $time_end  = microtime(true);
        $time_diff = ($time_end - $time_start);

    }
}