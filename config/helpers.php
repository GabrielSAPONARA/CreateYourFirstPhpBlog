<?php

if (!function_exists('ddd')) {
    function ddd(...$vars) {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0];
        echo "\n📂 Fichier : {$trace['file']} \n📌 Ligne : {$trace['line']}\n\n";
        dd(...$vars);
    }
}

if (!function_exists('dumpd')) {
    function dumpd(...$vars) {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0];
        echo "\n📂 Fichier : {$trace['file']} \n📌 Ligne : {$trace['line']}\n\n";
        dump(...$vars);
    }
}
