<?php

$add = fn($a) => fn($b) => $a + $b;
$multiply = fn($a) => fn($b) => $a * $b;

$extactArrayProp = fn($key) => fn($data) => $data[$key] ?? null;
$join = fn($glue) => fn($list) => implode($glue, $list);
$split = fn($delimiter) => fn($text) => explode($delimiter, $text);
$append = fn($tail) => fn($head) => $head . $tail;
$prepend = fn($head) => fn($tail) => $head . $tail;

$head = function ($list) {
    foreach ($list as $element) {
        return $element;
    }

    return null;
};
$tail = function ($list) {
    $newList = [];
    $head = true;
    foreach ($list as $key => $value) {
        if ($head) { $head = false; continue; }

        $newList[$key] = $value;
    }

    return $newList;
};
$equals = fn($a) => fn($b) => $a === $b;

return compact('add', 'multiply', 'extactArrayProp', 'join', 'split', 'append', 'prepend', 'head', 'tail', 'equals');