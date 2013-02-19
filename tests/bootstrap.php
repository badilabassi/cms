<?php

define('TEST_CONTENT', __DIR__ . '/content');
define('TEST_KIRBY_CORE', dirname(__DIR__));
define('TEST_KIRBY_LIB', TEST_KIRBY_CORE . '/lib');

$roots = array(
  'root'         => dirname(dirname(__DIR__)),
  'root.kirby'   => dirname(__DIR__),
  'root.site'    => __DIR__ . DIRECTORY_SEPARATOR . 'site',
  'root.content' => __DIR__ . DIRECTORY_SEPARATOR . 'content'
);

// include the kirby bootstrapper file
require_once(TEST_KIRBY_CORE . '/bootstrap.php');

// Simple Test Autorun
require_once(__DIR__ . '/simpletest/autorun.php');

