<?php // phpcs:ignore PSR1.Files.SideEffects.FoundWithSymbols

use DG\BypassFinals;

BypassFinals::enable();
define('OBJECT', 'OBJECT');
include_once __DIR__ . '/vendor/php-stubs/wordpress-stubs/wordpress-stubs.php';
