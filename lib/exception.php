<?php 

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Exception
 * 
 * Custom exception, which will be used to raise errors. 
 * Use the raise() helper to throw an exception
 * 
 * @package Kirby CMS
 */
class KirbyException extends Exception {

}