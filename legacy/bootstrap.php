<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

// autoloader for all legacy classes
function legacyLoader($class) {
  $file = ROOT_KIRBY_LEGACY . DS . strtolower($class) . '.php';
  if(file_exists($file)) require_once($file);
}

spl_autoload_register('legacyLoader');
