<?php

// testing "framework" :D https://gist.github.com/mathiasverraes/9046427
function it($m,$p){echo"\033[3",$p?'2m✔︎':'1m✘'.register_shutdown_function(function(){die(1);})," It $m\033[0m\n";}
function expect_error(Closure $f){try{$f(); return false;}catch(Throwable $e){return true;}}

// only curries the original function, does not curries returned callables! e.g.:
// f(a,b,c,d) will be curried until all arg given
// f(a,b) => g(c,d) will be curried only until f satisfied, then g will not be curried
$autocurry = function(callable $f, ...$args) use (&$autocurry) {
    $fargsReqCnt = (new ReflectionFunction($f))->getNumberOfRequiredParameters();

    if ($fargsReqCnt === 0) {
        return $f;
    }

    if (count($args) >= $fargsReqCnt) {
        return $f(...$args);
    }

    return fn(...$rargs) => $autocurry($f, ...array_merge($args, $rargs));
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

$add = fn($a, $b) => $a + $b;
$add = $autocurry($add);

it('curries add(a,b) so add(a)(b) is possible', $add(1)(2) === 3);
it('curries add(a,b) so add(a, b) is also possible', $add(1, 2) === 3);

$add = fn($a) => fn($b) => $a + $b;
$add = $autocurry($add);

it('curries only f(a) of f(a) => f(b) so add(a)(b) is possible', $add(1)(2) === 3);
it('curries only f(a) of f(a) => f(b) so add(a, b) is NOT possible', $add(1, 2) !== 3);
