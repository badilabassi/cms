<?php

set_exception_handler(function($exception) {
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

require(__DIR__ . DIRECTORY_SEPARATOR . 'bootstrap.php');

$site = site(array());

$site->rewrite();
$site->show();
