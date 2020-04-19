<?php

// testing "framework" :D https://gist.github.com/mathiasverraes/9046427
function it($m,$p){echo"\033[3",$p?'2m✔︎':'1m✘'.register_shutdown_function(function(){die(1);})," It $m\033[0m\n";}
function expect_error(Closure $f){try{$f(); return false;}catch(Throwable $e){return true;}}

$autocurry = function(callable $f, bool $evalValue = false, ...$args) use (&$autocurry) {
    $fargsCnt = (new ReflectionFunction($f))->getNumberOfParameters();
    $fargsReqCnt = (new ReflectionFunction($f))->getNumberOfRequiredParameters();

    if (count($args) >= $fargsReqCnt) {
        if ($evalValue) {
            $nargs = array_slice($args, 0, $fargsCnt);
            $rargs = array_slice($args, $fargsCnt);
            $result = $f(...$nargs);
            return is_callable($result) ? $autocurry($result, true, ...$rargs) : $result;
        }

        return $f;
    }

    return fn(...$rargs) => $autocurry($f, true, ...array_merge($args, $rargs));
};

// auto curry simple fuctions
$zeroArgFunc = fn() => 2;
$zeroArgFunc = $autocurry($zeroArgFunc);

it('returns result for zeroArgFunc()', $zeroArgFunc() === 2);
it('throws error for zeroArgFunc()()', expect_error(fn() => $zeroArgFunc()()));

$oneArgFunc = fn($a) => $a * 2;
$oneArgFunc = $autocurry($oneArgFunc);

it('returns result for oneArgFunc(2)', $oneArgFunc(2) === 4);
it('returns result for oneArgFunc()(2)', $oneArgFunc()(2) === 4);
it('returns result for oneArgFunc()()(2)', $oneArgFunc()()(2) === 4);

$twoArgFunc = fn($a, $b) => $a + $b;
$twoArgFunc = $autocurry($twoArgFunc);

it('returns result for twoArgFunc(1, 2)', $twoArgFunc(1, 2) === 3);
it('returns result for twoArgFunc(1)(2)', $twoArgFunc(1)(2) === 3);
it('returns result for twoArgFunc()(1, 2)', $twoArgFunc()(1, 2) === 3);
it('returns result for twoArgFunc()(1)(2)', $twoArgFunc()(1)(2) === 3);
it('returns result for twoArgFunc()()(1, 2)', $twoArgFunc()()(1, 2) === 3);
it('returns result for twoArgFunc()()(1)(2)', $twoArgFunc()()(1)(2) === 3);

$threeArgFunc = fn($a, $b, $c) => $a + $b + $c;
$threeArgFunc = $autocurry($threeArgFunc);

it('returns result for threeArgFunc(1, 2, 3)', $threeArgFunc(1, 2, 3) === 6);
it('returns result for threeArgFunc(1, 2)(3)', $threeArgFunc(1, 2)(3) === 6);
it('returns result for threeArgFunc(1)(2)(3)', $threeArgFunc(1)(2)(3) === 6);
it('returns result for threeArgFunc()(1)(2)(3)', $threeArgFunc()(1)(2)(3) === 6);
it('returns result for threeArgFunc()()(1)(2)(3)', $threeArgFunc()()(1)(2)(3) === 6);
it('returns result for threeArgFunc(1,2,3,4)', $threeArgFunc(1,2,3,4) === 6);
it('returns result for threeArgFunc(1)(2, 3)', $threeArgFunc(1)(2, 3) === 6);

$fourArgFunc = fn ($a, $b, $c, $d) => $a + $b + $c + $d;
$fourArgFunc = $autocurry($fourArgFunc);

it('returns result for fourArgFunc(1)(2, 3, 4)', $fourArgFunc(1)(2, 3, 4) === 10);
it('returns result for fourArgFunc(1, 2)(3, 4)', $fourArgFunc(1, 2)(3, 4) === 10);
it('returns result for fourArgFunc(1, 2, 3)(4)', $fourArgFunc(1, 2, 3)(4) === 10);
it('returns result for fourArgFunc(1)(2, 3)(4)', $fourArgFunc(1)(2, 3)(4) === 10);
it('returns result for fourArgFunc(1, 2)(3)(4)', $fourArgFunc(1, 2)(3)(4) === 10);
it('returns result for fourArgFunc(1, 2, 3)(4)', $fourArgFunc(1, 2, 3)(4) === 10);
it('returns result for fourArgFunc(1)(2)(3, 4)', $fourArgFunc(1)(2)(3, 4) === 10);

$threeArgFuncWithOptionalArg = fn($a, $b, $c = 0) => $a + $b + $c;
$threeArgFuncWithOptionalArg = $autocurry($threeArgFuncWithOptionalArg);

it('returns result for threeArgFuncWithOptionalArg(1, 2, 3)', $threeArgFuncWithOptionalArg(1, 2, 3) === 6);
it('returns result for threeArgFuncWithOptionalArg(1, 2)', $threeArgFuncWithOptionalArg(1, 2) === 3);
it('throws error for threeArgFuncWithOptionalArg(1, 2)(3)', expect_error(fn() => $threeArgFuncWithOptionalArg(1, 2)(3)));
it('returns result for threeArgFuncWithOptionalArg(1)(2, 3)', $threeArgFuncWithOptionalArg(1)(2, 3) === 6);
it('throws error for threeArgFuncWithOptionalArg(1)(2)(3)', expect_error(fn() => $threeArgFuncWithOptionalArg(1)(2)(3)));
it('returns result for threeArgFuncWithOptionalArg()(1)(2, 3)', $threeArgFuncWithOptionalArg()(1)(2, 3) === 6);
it('returns result for threeArgFuncWithOptionalArg()(1)(2)', $threeArgFuncWithOptionalArg()(1)(2) === 3);

// auto curry already curried functions
echo PHP_EOL.PHP_EOL;

$add = fn($a) => fn($b) => $a + $b;
$add = $autocurry($add);

it('returns result for add(1, 2)', $add(1, 2) === 3);
it('returns result for add(1)(2)', $add(1)(2) === 3);
it('returns result for add()(1, 2)', $add()(1, 2) === 3);
it('returns result for add()(1)(2)', $add()(1)(2) === 3);

$addThree = fn($a) => fn($b) => fn($c) => $a + $b + $c;
$addThree = $autocurry($addThree);

it('returns result for addThree(1, 2, 3)', $addThree(1, 2, 3) === 6);
it('returns result for addThree(1, 2)(3)', $addThree(1, 2)(3) === 6);
it('returns result for addThree(1)(2, 3)', $addThree(1)(2, 3) === 6);
it('returns result for addThree(1)(2)(3)', $addThree(1)(2)(3) === 6);

$addThreeWithOptional = fn($a) => fn($b, $c = 0) => $a + $b + $c;
$addThreeWithOptional = $autocurry($addThreeWithOptional);

it('returns result for addThreeWithOptional(1, 2, 3)', $addThreeWithOptional(1, 2, 3) === 6);
it('returns result for addThreeWithOptional(1, 2)', $addThreeWithOptional(1, 2) === 3);
it('throws error for addThreeWithOptional(1, 2)(3)', expect_error(fn() => $addThreeWithOptional(1, 2)(3)));
it('returns result for addThreeWithOptional(1)(2, 3)', $addThreeWithOptional(1)(2, 3) === 6);
it('throws error for addThreeWithOptional(1)(2)(3)', expect_error(fn() => $addThreeWithOptional(1)(2)(3)));
it('returns result for addThreeWithOptional(1)(2)', $addThreeWithOptional(1)(2) === 3);
