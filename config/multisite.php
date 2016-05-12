<?php

/**
 * Tell WordPress to be used as network
 */
define('WP_ALLOW_MULTISITE', true);

if (defined('WP_ALLOW_MULTISITE') && WP_ALLOW_MULTISITE) {
    define('MULTISITE', true);

    /**
     * Subdomain or subpath
     * Set to true for subdomain, false for subpath
     * Examples:
     * sub.domain.com (subdomain)
     * domain.com/sub (subpath)
     */
    define('SUBDOMAIN_INSTALL', false);

    /**
     * Default site config
     */
    define('DOMAIN_CURRENT_SITE', 'intranat.dev');
    define('PATH_CURRENT_SITE', '/');
    define('SITE_ID_CURRENT_SITE', 1);
    define('BLOG_ID_CURRENT_SITE', 1);
}
