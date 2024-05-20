<?php

require_once dirname(__DIR__) . '/../vendor/autoload.php';

require_once dirname(__DIR__) . '/../vendor/php-stubs/wordpress-stubs/wordpress-stubs.php';

// Bootstrap Patchwork
WP_Mock::setUsePatchwork(true);

// Bootstrap WP_Mock to initialize built-in features
WP_Mock::bootstrap();
