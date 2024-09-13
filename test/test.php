<?php

require_once '../vendor/autoload.php';

use JsonScout\JsonScout;
use JsonScout\JsonPath\Object\NodesType;



$query = "$..[?str_contains(str_to_lower(@), str_concat(1, 1, 'feat'))]";

$start = microtime(true);
$expr  = JsonScout::compile($query);
$end   = microtime(true);
$diff  = ($end - $start);

$result = $expr->execute(JsonScout::fromFile('./input.json'));

echo "Responded in (ms): {$diff}\n";
echo $result->toJson(NodesType::FLAG_NODES_AS_OBJECTS | NodesType::FLAG_INLINE,
                     JSON_PRETTY_PRINT);
