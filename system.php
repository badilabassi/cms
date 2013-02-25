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

// handle thrown exceptions and display a nice error page
set_exception_handler(function($exception) {
  // TODO: add a nice error page here
  echo $exception->getMessage();
});

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

// initialize the site for the first time
$site = site();

$site->rewrite();
$site->show();
