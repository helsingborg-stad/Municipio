<?php
/*
 * Plugin Name: Modularity Image Module
 * Plugin URI: -
 * Description: Image modue for Modularity
 * Version: 1.0
 * Author: Modularity
 */

define('IMAGE_MODULE_PATH', plugin_dir_path(__FILE__));

/**
 * Registers the module
 */
modularity_register_module(
    IMAGE_MODULE_PATH, // The directory path of the module
    'Image' // The class' file and class name (should be the same) withot .php extension
);
