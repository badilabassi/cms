<?php

if(!defined('TEST_CONTENT')) define('TEST_CONTENT', realpath(dirname(__FILE__) . '/../etc/testContent'));
if(!defined('TEST_SITE')) define('TEST_SITE', realpath(dirname(__FILE__) . '/../etc/testSite'));
if(!defined('TEST_KIRBY_CORE')) define('TEST_KIRBY_CORE', realpath(dirname(__FILE__) . '/../..'));
if(!defined('TEST_KIRBY_LIB')) define('TEST_KIRBY_LIB', TEST_KIRBY_CORE . '/lib');

define('KIRBY_INDEX_ROOT',   realpath(dirname(__FILE__) . '/../../..'));
define('KIRBY_CMS_ROOT',     realpath(dirname(__FILE__) . '/../..'));
define('KIRBY_SITE_ROOT', TEST_SITE);
define('KIRBY_CONTENT_ROOT', TEST_CONTENT);

// include the kirby bootstrapper file
require_once(TEST_KIRBY_CORE . '/bootstrap.php');

site(array(
  'url'       => 'http://superurl.com', 
  'subfolder' => '',
  'debug'     => true
));