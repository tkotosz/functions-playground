<?php

/**
 * @param string $module
 *
 * @return callable[]
 */
function requirefm(string $module): array
{
    static $cache;

    if (!isset($cache[$module])) {
        $path = 'inc/' . $module . '/index.php';
        
        $cache[$module] = (static function() use ($path) {
            return require($path);
        })();
    }

    return $cache[$module];
}

/**
 * @param string[] $functionNames
 * @param string   $module
 *
 * @return callable[]
 */
function import(array $functionNames, string $module): array
{
    $module = requirefm($module);

    $result = [];

    foreach ($functionNames as $functionName) {
        $result[] = $module[$functionName];
    }

    return $result;
}