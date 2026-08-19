<?php

require_once dirname(__DIR__) . '/library/Helper/BladeCache.php';

if (!in_array(PHP_SAPI, ['cli', 'phpdbg'], true)) {
    if (function_exists('http_response_code')) {
        http_response_code(403);
    }

    echo 'Forbidden: this script can only be run from CLI.' . PHP_EOL;
    exit(1);
}

require_once dirname(__DIR__) . '/vendor/autoload.php';

$results = \Municipio\Helper\BladeCache::clearConfiguredCacheDirectories(dirname(__DIR__), getenv('BLADE_CACHE_PATH') ?: null);
$hasFailures = false;

foreach ($results as $path => $status) {
    if ($status === 'failed') {
        $hasFailures = true;
    }

    echo strtoupper($status) . ': ' . $path . PHP_EOL;
}

exit($hasFailures ? 1 : 0);
