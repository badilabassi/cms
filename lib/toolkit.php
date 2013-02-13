<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Kirby -- A stripped down and easy to use toolkit for PHP
 *
 * @version 0.96
 * @author Bastian Allgeier <bastian@getkirby.com>
 * @link http://toolkit.getkirby.com
 * @copyright Copyright 2009-2012 Bastian Allgeier
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @package Kirby
 */

function autoload_toolkit($class) {
  $file = __DIR__ . DS . 'toolkit' . DS . $class . '.php';
  if(file_exists($file)) include $file;
}

spl_autoload_register('autoload_toolkit');

c::set('version', 0.96);
c::set('language', 'en');
c::set('charset', 'utf-8');
c::set('root', dirname(__FILE__));

/**
 * Redirects the user to a new URL
 *
 * @param   string    $url The URL to redirect to
 * @param   boolean   $code The HTTP status code, which should be sent (301, 302 or 303)
 * @package Kirby
 */
function go($url=false, $code=false) {

  if(empty($url)) $url = c::get('url', '/');

  // send an appropriate header
  if($code) {
    switch($code) {
      case 301:
        header('HTTP/1.1 301 Moved Permanently');
        break;
      case 302:
        header('HTTP/1.1 302 Found');
        break;
      case 303:
        header('HTTP/1.1 303 See Other');
        break;
    }
  }
  // send to new page
  header('Location:' . $url);
  exit();
}

/**
 * Returns the status from a Kirby response
 *
 * @param   array    $response The Kirby response array
 * @return  string   "error" or "success"
 * @package Kirby
 */
function status($response) {
  return a::get($response, 'status');
}

/**
 * Returns the message from a Kirby response
 *
 * @param   array    $response The Kirby response array
 * @return  string   The message
 * @package Kirby
 */
function msg($response) {
  return a::get($response, 'msg');
}

/**
 * Checks if a Kirby response is an error response or not. 
 *
 * @param   array    $response The Kirby response array
 * @return  boolean  Returns true if the response is an error, returns false if no error occurred 
 * @package Kirby
 */
function error($response) {
  return (status($response) == 'error') ? true : false;
}

/**
 * Checks if a Kirby response is a success response. 
 *
 * @param   array    $response The Kirby response array
 * @return  boolean  Returns true if the response is a success, returns false if an error occurred
 * @package Kirby
 */
function success($response) {
  return !error($response);
}

/**
 * Loads additional PHP files
 * 
 * You can set the root directory with c::set('root', 'my/root');
 * By default the same directory in which the kirby toolkit file is located will be used.
 * 
 * @param   args     A list filenames as individual arguments
 * @return  array    Returns a Kirby response array. On error the response array includes the unloadable files (errors).
 * @package Kirby
 */
function load() {

  $root   = c::get('root');
  $files  = func_get_args();
  $errors = array();

  foreach((array)$files AS $f) {
    $file = $root . '/' . $f . '.php';
    if(file_exists($file)) {
      include_once($file);
    } else {
      $errors[] = $file;
    }
  }
  
  if(!empty($errors)) return array(
    'status' => 'error',
    'msg'    => 'some files could not be loaded',
    'errors' => $errors
  );
  
  return array(
    'status' => 'success',
    'msg'    => 'all files have been loaded'
  );

}