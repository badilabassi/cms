<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Params
 *
 * The params object is a child object of the URI object. 
 * It contains all named parameters from a URL: 
 * param1:value1/param2:value2/param3:value3
 * 
 * @package Kirby CMS
 */
class KirbyUriParams extends KirbyCollection {

  /**
   * Returns all params in a single string
   * 
   * @return string
   */
  public function toString() {
    $output = array();
    $sep    = (DS == '/') ? ':' : ';';
    foreach($this->toArray() as $key => $value) {
      $output[] = $key . $sep . $value;
    }        
    return implode('/', $output);
  }

  /**
   * Converts the object to a string
   * 
   * @return string
   */
  public function __toString() {
    return $this->toString();
  }

}