<?php

/**
 * Kirby System file
 * This is used by the index.php to load the bootstrapper 
 * and initialize the site. It also makes sure to be compatible
 * with old index.php files from recent versions
 * 
 * @package Kirby CMS
 */

// legacy root handling
if(!defined('ROOT')) {

  // system independent shortcut for /
  define('DS', DIRECTORY_SEPARATOR);

  // grab and define custom roots from the index.php
  define('ROOT',         $root);
  define('ROOT_KIRBY',   $rootKirby); 
  define('ROOT_SITE',    $rootSite);
  define('ROOT_CONTENT', $rootContent);  

}

// load the bootstrapper
require(ROOT_KIRBY . DS . 'bootstrap.php');

// create a exception handler old school style to be compatible with PHP 5.2
$exceptionHandler = create_function('$exception', 'require(ROOT_KIRBY_MODALS . DS . "exception.php"); exit();');

// handle thrown exceptions and display a nice error page
set_exception_handler($exceptionHandler);

// initialize the site for the first time
$site = site();

$site->rewrite();
$site->show();
