<?php

define('TEST_URL', '');
define('TEST_CONTENT', __DIR__ . '/testContent');
define('TEST_KIRBY_CORE', dirname(__DIR__));
define('TEST_KIRBY_LIB', TEST_KIRBY_CORE . '/lib');

if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

// include the kirby bootstrapper file
require_once(TEST_KIRBY_CORE . '/bootstrap.php');

site(array(
  'url'          => 'http://superurl.com', 
  'subfolder'    => '',
  'root.content' => TEST_CONTENT, 
  'root.site'    => TEST_CONTENT
));

// Simple Test Autorun
require_once(__DIR__ . '/simpletest/autorun.php');

