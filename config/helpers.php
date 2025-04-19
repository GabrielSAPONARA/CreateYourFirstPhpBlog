<?php

if (!function_exists('ddd')) {
    function ddd(...$vars) {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0];
        printf("\n📂 Fichier : %s \n📌 Ligne : %d\n\n", $trace['file'], $trace['line']);

        dd(...$vars);
    }
}

if (!function_exists('dumpd')) {
    function dumpd(...$vars) {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0];
        printf("\n📂 Fichier : %s \n📌 Ligne : %d\n\n", $trace['file'], $trace['line']);
        dump(...$vars);
    }
}
