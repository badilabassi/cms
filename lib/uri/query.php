<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Query
 *
 * The query object is a child object of the URI object. 
 * It contains all query keys from a URL: 
 * var1=value1&var2=value2&var3=value3
 * 
 * @package Kirby CMS
 */
class KirbyUriQuery extends KirbyCollection {
  
  /**
   * Returns the query in a single string
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
    return http_build_query($this->toArray());    
  }

}

