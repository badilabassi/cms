<?php

/**
 * Kirby Bootstrapper
 * 
 * Include this file to load all essential 
 * files to initiate a new Kirby site
 */

// build a default roots array if not available
if(!isset($roots)) {
  $dir   = dirname(__FILE__);
  $root  = dirname($dir);
  $roots = array(
    'root'         => $root,
    'root.kirby'   => $dir,
    'root.site'    => $root . DIRECTORY_SEPARATOR . 'site',
    'root.content' => $root . DIRECTORY_SEPARATOR . 'content'
  );
}

// global key for direct inclusion protection
define('KIRBY', true);

// shortcut for the directory separator
define('DS', DIRECTORY_SEPARATOR);

// build constants for all roots
define('ROOT',                $roots['root']);

// the content folder
define('ROOT_CONTENT',        $roots['root.content']);

// all stuff in the main kirby folder
define('ROOT_KIRBY',          $roots['root.kirby']);
define('ROOT_KIRBY_LIB',      ROOT_KIRBY . DS . 'lib');
define('ROOT_KIRBY_TAGS',     ROOT_KIRBY . DS . 'tags');
define('ROOT_KIRBY_PARSERS',  ROOT_KIRBY . DS . 'parsers');
define('ROOT_KIRBY_MODALS',   ROOT_KIRBY . DS . 'modals');

// all stuff in the site folder
define('ROOT_SITE',           $roots['root.site']);
define('ROOT_SITE_CACHE',     ROOT_SITE . DS . 'cache');
define('ROOT_SITE_TEMPLATES', ROOT_SITE . DS . 'templates');
define('ROOT_SITE_SNIPPETS',  ROOT_SITE . DS . 'snippets');
define('ROOT_SITE_CONFIG',    ROOT_SITE . DS . 'config');
define('ROOT_SITE_PLUGINS',   ROOT_SITE . DS . 'plugins');
define('ROOT_SITE_LANGUAGES', ROOT_SITE . DS . 'languages');
define('ROOT_SITE_TAGS',      ROOT_SITE . DS . 'tags');

// load the kirby toolkit
include(ROOT_KIRBY_LIB . DS . 'kirby.php');

// autoloader for all classes in the lib
function autoload($class) {
  $file = ROOT_KIRBY_LIB . DS . strtolower(str_replace('Kirby', '', $class)) . '.php';
  if(file_exists($file)) include $file;
}

spl_autoload_register('autoload');

// load all default config values
include(ROOT_KIRBY . DS . 'defaults.php');

// load all helper functions
include(ROOT_KIRBY_LIB . DS . 'helpers.php');

// load all legacy code adapters
include(ROOT_KIRBY_LIB . DS . 'legacy.php');

// load the main site class
include(ROOT_KIRBY_LIB . DS . 'site.php');