<?php

/**
 * Kirby System file
 * This is used by the index.php to load the bootstrapper 
 * and initialize the site. It also makes sure to be compatible
 * with old index.php files from recent versions
 * 
 * @package   Kirby CMS
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://getkirby.com/license
 */

// legacy root handling
if(!defined('KIRBY_INDEX_ROOT')) {

  // location of the index.php / public root  
  define('KIRBY_INDEX_ROOT', $root);
  
  // location of the kirby system
  define('KIRBY_CMS_ROOT', $rootKirby); 
  
  // location of the site folder
  define('KIRBY_SITE_ROOT', $rootSite);
  
  // location of the content folder
  define('KIRBY_CONTENT_ROOT', $rootContent);  

}

// handle thrown exceptions and display a nice error page
set_exception_handler(function($exception) {  
    require(KIRBY_CMS_ROOT_MODALS . DS . 'exception.php'); 
  exit();
});

// catch all errors and throw an exception so it can get caught by kirby's error screen
set_error_handler(function($code, $message, $file, $line) {  
  switch($code) {
    case E_NOTICE:
    case E_USER_NOTICE:
      return;
    default:
      throw new ErrorException($message, 0, $code, $file, $line);  
  }
});

// load the bootstrapper
require(KIRBY_CMS_ROOT . DIRECTORY_SEPARATOR . 'bootstrap.php');

// initialize the site for the first time
$site = site::instance();

// show the troubleshoot modal if required
$site->troubleshoot();

// enable rewriting of unwanted URLs
$site->rewrite();

// display the site
$site->show();