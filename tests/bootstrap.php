<?php

define('TEST_URL', '');
define('TEST_CONTENT', __DIR__ . '/content');
define('TEST_KIRBY_CORE', dirname(__DIR__));
define('TEST_KIRBY_LIB', TEST_KIRBY_CORE . '/lib');

if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

// include the kirby bootstrapper file
require_once(TEST_KIRBY_CORE . '/bootstrap.php');

// Simple Test Autorun
require_once(__DIR__ . '/simpletest/autorun.php');
