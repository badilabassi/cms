<?php

if(!defined('TEST_CONTENT')) define('TEST_CONTENT', __DIR__ . '/testContent');
if(!defined('TEST_SITE')) define('TEST_SITE', __DIR__ . '/testSite');
if(!defined('TEST_KIRBY_CORE')) define('TEST_KIRBY_CORE', dirname(__DIR__));
if(!defined('TEST_KIRBY_LIB')) define('TEST_KIRBY_LIB', TEST_KIRBY_CORE . '/lib');

$roots = array(
  'root'         => dirname(dirname(__DIR__)),
  'root.kirby'   => dirname(__DIR__),
  'root.site'    => TEST_SITE,
  'root.content' => TEST_CONTENT
);

// include the kirby bootstrapper file
require_once(TEST_KIRBY_CORE . '/bootstrap.php');

site(array(
  'url'       => 'http://superurl.com', 
  'subfolder' => '',
));