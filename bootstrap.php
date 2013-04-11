<?php

/**
 * Kirby Bootstrapper
 * 
 * Include this file to load all essential 
 * files to initiate a new Kirby site
 */

// global key for direct inclusion protection
if(!defined('KIRBY')) define('KIRBY', true);

// shortcut for the directory separator
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

// last exit to define basic roots
if(!defined('ROOT_KIRBY'))          define('ROOT_KIRBY',         dirname(__FILE__));
if(!defined('ROOT'))                define('ROOT',               dirname(ROOT_KIRBY));
if(!defined('ROOT_CONTENT'))        define('ROOT_CONTENT',       ROOT . DS . 'content');
if(!defined('ROOT_SITE'))           define('ROOT_SITE',          ROOT . DS . 'site');
if(!defined('ROOT_KIRBY_TOOLKIT'))  define('ROOT_KIRBY_TOOLKIT', ROOT_KIRBY . DS . 'toolkit');

// all stuff in the main kirby folder
define('ROOT_KIRBY_LIB',      ROOT_KIRBY . DS . 'lib');
define('ROOT_KIRBY_LEGACY',   ROOT_KIRBY . DS . 'legacy');
define('ROOT_KIRBY_TAGS',     ROOT_KIRBY . DS . 'tags');
define('ROOT_KIRBY_PARSERS',  ROOT_KIRBY . DS . 'parsers');
define('ROOT_KIRBY_PLUGINS',  ROOT_KIRBY . DS . 'plugins');
define('ROOT_KIRBY_MODALS',   ROOT_KIRBY . DS . 'modals');

// all stuff in the site folder
define('ROOT_SITE_CACHE',     ROOT_SITE . DS . 'cache');
define('ROOT_SITE_TEMPLATES', ROOT_SITE . DS . 'templates');
define('ROOT_SITE_SNIPPETS',  ROOT_SITE . DS . 'snippets');
define('ROOT_SITE_CONFIG',    ROOT_SITE . DS . 'config');
define('ROOT_SITE_PLUGINS',   ROOT_SITE . DS . 'plugins');
define('ROOT_SITE_LANGUAGES', ROOT_SITE . DS . 'languages');
define('ROOT_SITE_TAGS',      ROOT_SITE . DS . 'tags');

// load the toolkit
require_once(ROOT_KIRBY_TOOLKIT . DS . 'bootstrap.php');

// autoloader for all classes in the lib
function libLoader($class) {
  
  // library stuff
  $file = ROOT_KIRBY_LIB . DS . strtolower(str_replace('Kirby', '', $class)) . '.php';
  
  if(file_exists($file)) {
    require_once($file);
    return;
  } 

}

spl_autoload_register('libLoader');

// load all default config values
include(ROOT_KIRBY . DS . 'defaults.php');

// load the legacy bootstrapper
include(ROOT_KIRBY_LEGACY . DS . 'bootstrap.php');

// load all helper functions
include(ROOT_KIRBY_LIB . DS . 'helpers.php');

// load the main site class
include(ROOT_KIRBY_LIB . DS . 'site.php');