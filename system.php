<?php

// direct access protection
if(!isset($root) && !isset($roots)) die('Direct access is not allowed');

/**
 * Kirby System file
 * This is used by the index.php to load the bootstrapper 
 * and initialize the site. It also makes sure to be compatible
 * with old index.php files from recent versions
 * 
 * @package Kirby CMS
 */

// legacy root handling
if(!isset($roots)) {

  $roots = array(
    'root'         => $root, 
    'root.kirby'   => $rootKirby,
    'root.site'    => $rootSite,
    'root.content' => $rootContent,
  );

}

// load the bootstrapper
require(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'bootstrap.php');

// create a exception handler old school style to be compatible with PHP 5.2
$exceptionHandler = create_function('$exception', 'require(ROOT_KIRBY_MODALS . DS . "exception.php"); exit();');

// handle thrown exceptions and display a nice error page
set_exception_handler($exceptionHandler);

// initialize the site for the first time
$site = site();
$site->rewrite();
$site->show();
