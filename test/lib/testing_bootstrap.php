<?php

if(!defined('TEST_CONTENT')) define('TEST_CONTENT', realpath(dirname(__FILE__) . '/../etc/testContent'));
if(!defined('TEST_SITE')) define('TEST_SITE', realpath(dirname(__FILE__) . '/../etc/testSite'));
if(!defined('TEST_KIRBY_CORE')) define('TEST_KIRBY_CORE', realpath(dirname(__FILE__) . '/../..'));
if(!defined('TEST_KIRBY_LIB')) define('TEST_KIRBY_LIB', TEST_KIRBY_CORE . '/lib');

$roots = array(
  'root'         => realpath(dirname(__FILE__) . '/../../..'),
  'root.kirby'   => realpath(dirname(__FILE__) . '/../..'),
  'root.site'    => TEST_SITE,
  'root.content' => TEST_CONTENT
);

// include the kirby bootstrapper file
require_once(TEST_KIRBY_CORE . '/bootstrap.php');

site(array(
  'url'       => 'http://superurl.com', 
  'subfolder' => '',
));