<?php

/**
 * Kirby Bootstrapper
 * 
 * Include this file to load all essential 
 * files to initiate a new Kirby site
 */

if(!defined('DS'))    define('DS', DIRECTORY_SEPARATOR);
if(!defined('ROOT'))  define('ROOT', dirname(__DIR__));
if(!defined('LIB'))   define('LIB', ROOT . DS . 'kirby' . DS . 'lib');
if(!defined('KIRBY')) define('KIRBY', true);

// load the kirby toolkit
include(LIB . DS . 'kirby.php');

function autoload($class) {
  $file = LIB . DS . strtolower(str_replace('Kirby', '', $class)) . '.php';
  if(file_exists($file)) include $file;
}

spl_autoload_register('autoload');

// load all default config values
include(__DIR__ . DS . 'defaults.php');

// load all helper functions
include(LIB . DS . 'helpers.php');

// load all legacy code adapters
include(LIB . DS . 'legacy.php');

// load the main site class
include(LIB . DS . 'site.php');