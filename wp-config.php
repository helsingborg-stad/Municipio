<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

require_once 'config/cache.php';
require_once 'config/cookie.php';
require_once 'config/content.php';
require_once 'config/database.php';
require_once 'config/salts.php';
require_once 'config/plugins.php';
require_once 'config/update.php';
require_once 'config/upload.php';

/**
 * Multisite settings
 *
 * To enable this site as a multisite please rename the config/multisite-example.php file to
 * multisite.php, then go ahead and edit the configurations
 */
if (file_exists(__DIR__ . '/config/multisite.php')) {
    require_once 'config/multisite.php';
}

/**
 * Developer settings
 *
 * You can create a file called "developer.php" in the config dir and
 * put your dev-stuff and overrides inside.
 */
if (file_exists(__DIR__ . '/config/developer.php')) {
    require_once 'config/developer.php';
}

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'intranat_';


/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
    define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
