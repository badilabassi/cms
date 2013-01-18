<?php

/**
 * Kirby Bootstrapper
 * 
 * Include this file to load all essential 
 * files to initiate a new Kirby site
 */

if(!defined('ROOT'))  define('ROOT', dirname(__DIR__));
if(!defined('KIRBY')) define('KIRBY', true);
if(!defined('DS'))    define('DS', DIRECTORY_SEPARATOR);

// load the kirby toolkit
require(__DIR__ . DS . 'lib' . DS . 'kirby.php');

// load all default config values
require(__DIR__ . DS . 'defaults.php');

// load the main site class
require(__DIR__ . DS . 'lib' . DS . 'site.php');