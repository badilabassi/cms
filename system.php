<?php

set_exception_handler(function($exception) {
  echo $exception->getMessage();
});

define('ROOT', $root);

require(__DIR__ . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'timer.php');
require(__DIR__ . DIRECTORY_SEPARATOR . 'bootstrap.php');

$site = site(array(
  'root.content'   => $rootContent,
  'root.kirby'     => $rootKirby,
  'root.lib'       => $rootKirby . DS . 'lib',
  'root.parsers'   => $rootKirby . DS . 'parsers',
  'root.modals'    => $rootKirby . DS . 'modals',
  'root.site'      => $rootSite,
  'root.cache'     => $rootSite . DS . 'cache',
  'root.templates' => $rootSite . DS . 'templates',
  'root.snippets'  => $rootSite . DS . 'snippets',
  'root.config'    => $rootSite . DS . 'config',
  'root.plugins'   => $rootSite . DS . 'plugins',
  'root.languages' => $rootSite . DS . 'languages',
));

$site->rewrite();
$site->show();

echo timer::get();
