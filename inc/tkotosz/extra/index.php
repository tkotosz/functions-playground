<?php

$add = fn($a) => fn($b) => $a + $b;
$multiply = fn($a) => fn($b) => $a * $b;

$extactArrayProp = fn($key) => fn($data) => $data[$key] ?? null;
$join = fn($glue) => fn($list) => join($glue, $list);
$append = fn($tail) => fn($head) => $head . $tail;
$prepend = fn($head) => fn($tail) => $head . $tail;

return compact('add', 'multiply', 'extactArrayProp', 'join', 'append', 'prepend');