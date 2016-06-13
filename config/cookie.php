<?php

/**
 * Tell WordPress to save the cookie on the domain
 * @var bool
 */

if (strpos($_SERVER['HTTP_HOST'], "helsingborg.se") !== false) {
    define('COOKIE_DOMAIN', ".helsingborg.se");
} else {
    define('COOKIE_DOMAIN', $_SERVER['HTTP_HOST']);
}
