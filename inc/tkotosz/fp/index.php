<?php

$map = fn($fn) => fn($list) => array_map($fn, $list);
$reduceRight = fn($fn, $initial) => fn($list) => array_reduce(array_reverse($list), $fn, $initial);
$reduceLeft = fn($fn, $initial = null) => fn($list) => array_reduce($list, $fn, $initial);
$reduce = $reduceLeft;
$filter = fn($cond) => fn($list) => array_filter($list, $cond);

$compose = fn(...$fns) => fn($x) => $reduceRight(fn($v, $f) => $f($v), $x)($fns);
$pipe = fn(...$fns) => fn($x) => $reduceLeft(fn($v, $f) => $f($v), $x)($fns);
$partial = fn($f, ...$args) => fn(...$remainingArgs) => $f(...$merge($args, $remainingArgs));

$merge = 'array_merge';
$not = fn($f) => fn(...$args) => !$f(...$args);
$isNull = fn($x) => $x === null;

return compact(
    'map',
    'reduce',
    'reduceLeft',
    'reduceRight',
    'filter',
    'compose',
    'pipe',
    'merge',
    'partial',
    'not',
    'isNull'
);