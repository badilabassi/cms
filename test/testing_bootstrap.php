<?php

if(!defined('TEST_CONTENT')) define('TEST_CONTENT', dirname(__FILE__) . '/testContent');
if(!defined('TEST_SITE')) define('TEST_SITE', dirname(__FILE__) . '/testSite');
if(!defined('TEST_KIRBY_CORE')) define('TEST_KIRBY_CORE', dirname(dirname(__FILE__)));
if(!defined('TEST_KIRBY_LIB')) define('TEST_KIRBY_LIB', TEST_KIRBY_CORE . '/lib');

$roots = array(
  'root'         => dirname(dirname(dirname(__FILE__))),
  'root.kirby'   => dirname(dirname(__FILE__)),
  'root.site'    => TEST_SITE,
  'root.content' => TEST_CONTENT
);

// include the kirby bootstrapper file
require_once(TEST_KIRBY_CORE . '/bootstrap.php');

try {
  site(array(
    'url'       => 'http://superurl.com', 
    'subfolder' => '',
  ));
} catch(KirbyException $e) {
  echo $e->getMessage();
}