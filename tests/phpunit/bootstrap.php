<?php

if (getenv('PHPUNIT_GROUP') === 'WP_MOCK') {
    WP_Mock::setUsePatchwork(true);
    WP_Mock::bootstrap();
    WP_Mock::userFunction('is_wp_error', [ 'return' => function ($object) {
        return $object instanceof WP_Error;
    } ]);
}
