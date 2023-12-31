<?php

function debug(...$vars) {
    http_response_code(417);

    foreach ($vars as $var) {
        echo("<pre>");
        print_r($var);
        echo("</pre>");
    }

    die();
}

$viewCustomGlobals = array();
function defineView($key, $var) {
    global $viewCustomGlobals;

    $viewCustomGlobals[$key] = $var;
}

function rglob($pattern, $flags = 0) {
    $files = glob($pattern, $flags);
    foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
        $files = array_merge($files, rglob($dir.'/'.basename($pattern), $flags));
    }
    return $files;
}