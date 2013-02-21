<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Path
 *
 * The path object is a child object of the URI object. 
 * It contains all parts of the path from a URL: 
 * this/is/a/beautiful/path
 * 
 * @package Kirby CMS
 */
class KirbyUriPath extends KirbyCollection {
  
  /**
   * Returns the entire path in a single string
   * 
   * @return string
   */
  public function __toString() {
    return $this->toString();
  }
 
  /**
   * Converts the object to a string
   * 
   * @return string
   */
  public function toString() {
    return implode('/', $this->_);  
  }
    
}
