<?php

$add = fn($a) => fn($b) => $a + $b;
$multiply = fn($a) => fn($b) => $a * $b;

$extactArrayProp = fn($key) => fn($data) => $data[$key] ?? null;
$join = fn($glue) => fn($list) => implode($glue, $list);
$split = fn($delimiter) => fn($text) => explode($delimiter, $text);
$append = fn($tail) => fn($head) => $head . $tail;
$prepend = fn($head) => fn($tail) => $head . $tail;

return compact('add', 'multiply', 'extactArrayProp', 'join', 'split', 'append', 'prepend');