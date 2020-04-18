<?php

// built-in array_map doesn't provide key to the callback
$map = fn($fn) => function($list) use (&$fn) {
    $result = [];

    foreach ($list as $key => $value) {
        $result[$key] = $fn($value, $key);
    }

    return $result;
};

// built-in array_reduce doesn't provide key to the callback
$reduce = $reduceLeft = fn($fn, $initial = null) => function($list) use (&$fn, $initial) {
    $acc = $initial;

    foreach ($list as $key => $value) {
        $acc = $fn($acc, $value, $key);
    }

    return $acc;
};

$reduceRight = fn($fn, $initial = null) => function($list) use (&$fn, $initial) {
    $acc = $initial;

    foreach (array_reverse($list, true) as $key => $value) {
        $acc = $fn($acc, $value, $key);
    }

    return $acc;
};

$filter = fn($cond) => fn($list) => array_filter($list, $cond);

$compose = fn(...$fns) => fn($x) => $reduceRight(fn($v, $f) => $f($v), $x)($fns);
$pipe = fn(...$fns) => fn($x) => $reduceLeft(fn($v, $f) => $f($v), $x)($fns);
$partial = fn($f, ...$args) => fn(...$remainingArgs) => $f(...$merge($args, $remainingArgs));

$merge = 'array_merge';
$mergeRecursive = 'array_merge_recursive';
$not = fn($f) => fn(...$args) => !$f(...$args);
$isNull = fn($x) => $x === null;
$indentity = fn($x) => $x;

return compact(
    'map',
    'reduce',
    'reduceLeft',
    'reduceRight',
    'filter',
    'compose',
    'pipe',
    'merge',
    'mergeRecursive',
    'partial',
    'not',
    'isNull',
    'indentity'
);