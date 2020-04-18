<?php

require __DIR__ . '/inc/function_autoload.php';

/**
 * increment
 */
[$add] = import(['add'], 'tkotosz/extra');

$increment = $add(1);

echo $increment(2) . PHP_EOL; // 3


/**
 * Filter null values
 */
[$filter, $isNull, $not] = import(['filter', 'isNull', 'not', 'pipe'], 'tkotosz/fp');

$removeNulls = $filter($not($isNull));

print_r($removeNulls(['foo', null, 'bar', 'baz', null, null])) . PHP_EOL; // ['foo', 'bar', 'baz']


/**
 * Reverse list
 */
[$reduce, $merge] = import(['reduce', 'merge'], 'tkotosz/fp');

//$reverse = 'array_reverse';
$reverse = $reduce(fn($acc, $v) => $merge([$v], $acc), []);

print_r($reverse([1, 2, 3])) . PHP_EOL; // [3, 2, 1]


/**
 * Pipe pipe pipe
 */
[$pipe, $map] = import(['pipe', 'map'], 'tkotosz/fp');
[$add, $multiply] = import(['add', 'multiply'], 'tkotosz/extra');

$double = $multiply(2);
$increment = $add(1);

$doubleAndIncrement = $pipe(
    $double,
    $increment
);
$doubleAndIncrementAll = $map($doubleAndIncrement);

echo $doubleAndIncrement(5) . PHP_EOL; // 11
print_r($doubleAndIncrementAll([1, 2, 3])) . PHP_EOL; // [3, 5, 7]


/**
 * As an appetizer, here’s a puzzle — pick all the first names from a list of people, and print them joined by a comma (“,”).
 * https://medium.com/@jondot/functional-programming-with-python-for-people-without-time-1eebdbd9526c
 */

$people = [
    [
        'first_name' => 'Bruce',
        'last_name' => 'Wayne'
    ],
    [
        'first_name' => 'Joker',
        'last_name' => ''
    ]
];

[$map, $pipe] = import(['map', 'pipe'], 'tkotosz/fp');
[$extract, $join, $append] = import(['extactArrayProp', 'join', 'append'], 'tkotosz/extra');

$extractFirstNames = $map($extract('first_name'));
$joinWithComma = $join(',');
$appendNewLine = $append(PHP_EOL);
$printList = 'print_r';

$processor = $pipe(
    $extractFirstNames,
    $joinWithComma,
    $appendNewLine,
    $printList
);

$processor($people);


/**
 * Task: Create converter
 * Input:
 * [
 *   'key1/key2/key2' => 'Hello',
 *   'key1/key2/key3' => 'Goodbye',
 *   'key2' => 'Foobar'
 * ]
 * Output:
 * [
 *   'key1' => [
 *       'key2' => [
 *           'key2'=> 'Hello',
 *           'key3' => 'Goodbye',
 *       ],
 *   'key2' => 'Foobar',
 * ]
 */

$input = [
    'key1/key2/key2' => 'Hello',
    'key1/key2/key3' => 'Goodbye',
    'key2' => 'Foobar'
];

[$reduce, $mergeRecursive] = import(['reduce', 'mergeRecursive'], 'tkotosz/fp');
[$split] = import(['split'], 'tkotosz/extra');

// hmmm...
$buildNestedArray = function($keys, $value) {
    $target = [];

    $current = &$target;
    foreach($keys as $index) {
        $current = &$current[$index];
    }
    $current = $value;

    return $target;
};
$buildArrayFromKeyPath = fn($keyPath, $value) => $buildNestedArray($split('/')($keyPath), $value);
$processor = $reduce(fn($acc, $value, $key) => $mergeRecursive($acc, $buildArrayFromKeyPath($key, $value)), []);

print_r($processor($input)) . PHP_EOL;


/**
 * grab the "review" element's type
 * source is (mostly just copy pasted to see if it works :D):
 * 
 * https://ramdajs.com/repl/?v=0.25.0#?const%20val%20%3D%20%27review-inread%2Creview-related%2Creview-upnext%2Cnews-inread%2Cnews-related%27%0A%0Aconst%20byReview%20%3D%20function%28val%29%20%7B%0A%20%20return%20equals%28%27review%27%2C%20head%28val%29%29%3B%0A%7D%0A%0Acompose%28%0A%20%20map%28head%29%2C%0A%20%20map%28tail%29%2C%0A%20%20filter%28byReview%29%2C%0A%20%20map%28split%28%27-%27%29%29%2C%0A%20%20split%28%27%2C%27%29%0A%29%28val%29
 */
$input = 'review-inread,review-related,review-upnext,news-inread,news-related';

[$pipe] = import(['pipe'], 'tkotosz/fp');
[$split, $head, $tail, $equals] = import(['split', 'head', 'tail', 'equals'], 'tkotosz/extra');

$byReview = $pipe($head, $equals('review'));
$getType = $pipe($tail, $head);

$res = $pipe(
  $split(','),
  $map($split('-')),
  $filter($byReview),
  $map($getType)
)($input);

var_dump($res);