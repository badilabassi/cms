<?php

/**
 * Kirby Bootstrapper
 * 
 * Include this file to load all essential 
 * files to initiate a new Kirby site
 * 
 * @package   Kirby CMS
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://getkirby.com/license
 */

/**
 * Helper constants
 */

if(!defined('KIRBY'))     define('KIRBY',     true);
if(!defined('DS'))        define('DS',        DIRECTORY_SEPARATOR);
if(!defined('MB_STRING')) define('MB_STRING', (int)function_exists('mb_get_info'));

/**
 * Overwritable constants
 * Define them before including the bootstrapper
 * to change essential roots
 */

// location of the kirby cms system directory
if(!defined('KIRBY_CMS_ROOT')) define('KIRBY_CMS_ROOT', __DIR__);

// location of the kirby toolkit, which should be used for the cms
if(!defined('KIRBY_CMS_ROOT_TOOLKIT')) define('KIRBY_CMS_ROOT_TOOLKIT', KIRBY_CMS_ROOT . DS . 'toolkit');

// location of the index.php / public root
if(!defined('KIRBY_INDEX_ROOT')) define('KIRBY_INDEX_ROOT', dirname(KIRBY_CMS_ROOT));

// location of the content directory
if(!defined('KIRBY_CONTENT_ROOT')) define('KIRBY_CONTENT_ROOT', KIRBY_INDEX_ROOT . DS . 'content');

// location of the site directory with all site specific files and configs
if(!defined('KIRBY_SITE_ROOT')) define('KIRBY_SITE_ROOT', KIRBY_INDEX_ROOT . DS . 'site');

/**
 * Fixed constants
 * Those cannot and should not be overwritten
 */

// cms internals
define('KIRBY_CMS_ROOT_CONFIG',  KIRBY_CMS_ROOT . DS . 'config');
define('KIRBY_CMS_ROOT_LIB',     KIRBY_CMS_ROOT . DS . 'lib');
define('KIRBY_CMS_ROOT_TAGS',    KIRBY_CMS_ROOT . DS . 'tags');
define('KIRBY_CMS_ROOT_PARSERS', KIRBY_CMS_ROOT . DS . 'parsers');
define('KIRBY_CMS_ROOT_PLUGINS', KIRBY_CMS_ROOT . DS . 'plugins');
define('KIRBY_CMS_ROOT_MODALS',  KIRBY_CMS_ROOT . DS . 'modals');

// project folder internals
define('KIRBY_SITE_ROOT_CACHE',     KIRBY_SITE_ROOT . DS . 'cache');
define('KIRBY_SITE_ROOT_TEMPLATES', KIRBY_SITE_ROOT . DS . 'templates');
define('KIRBY_SITE_ROOT_SNIPPETS',  KIRBY_SITE_ROOT . DS . 'snippets');
define('KIRBY_SITE_ROOT_CONFIG',    KIRBY_SITE_ROOT . DS . 'config');
define('KIRBY_SITE_ROOT_LANGUAGES', KIRBY_SITE_ROOT . DS . 'languages');
define('KIRBY_SITE_ROOT_PLUGINS',   KIRBY_SITE_ROOT . DS . 'plugins');
define('KIRBY_SITE_ROOT_TAGS',      KIRBY_SITE_ROOT . DS . 'tags');

// load the toolkit
require_once(KIRBY_CMS_ROOT_TOOLKIT . DS . 'bootstrap.php');

// initialize the autoloader
$autoloader = new Kirby\Toolkit\Autoloader();

// set the base root where all classes are located
$autoloader->root = KIRBY_CMS_ROOT_LIB;

// set the global namespace for all classes
$autoloader->namespace = 'Kirby\\CMS';

// add all needed aliases
$autoloader->aliases = array(
  'file'      => 'Kirby\\CMS\\File',
  'files'     => 'Kirby\\CMS\\Files',
  'kirbytext' => 'Kirby\\CMS\\Kirbytext',
  'page'      => 'Kirby\\CMS\\Page',
  'pages'     => 'Kirby\\CMS\\Pages',
  'plugin'    => 'Kirby\\CMS\\Plugin',
  'site'      => 'Kirby\\CMS\\Site',
  'variable'  => 'Kirby\\CMS\\Variable',
);

// start autoloading
$autoloader->start();

// load all default config values
include(KIRBY_CMS_ROOT_CONFIG . DS . 'defaults.php');

// load all default events
include(KIRBY_CMS_ROOT_CONFIG . DS . 'events.php');

// load all helper functions
include(KIRBY_CMS_ROOT . DS . 'helpers.php');