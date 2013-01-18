<?php 

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Variable
 *
 * Each custom variable from content text 
 * files in Kirby is stored as an object. 
 * This makes it possible to retreive additional 
 * information about each variable value as the parent
 * page and related content file
 * 
 * @package Kirby CMS
 */
class KirbyVariable {

  // the key of this variable
  protected $key = null;
  
  // the value of this variable
  protected $value = null;

  // the parent KirbyPage object
  protected $page = null;

  // the parent KirbyContent file object
  protected $file = null;

  /**
   * Constructor
   * 
   * @param string $key The name of the key for this variable
   * @param mixed $value The value for this variable
   * @param object $file The parent KirbyContent object
   */
  public function __construct($key, $value, KirbyContent $file = null) {
    $this->key   = $key;
    $this->value = $value;
    $this->file  = $file;  
  }

  /**
   * Returns the key for this variable
   * This equals the field name in the content text file
   * 
   * @return string 
   */
  public function key() {
    return $this->key;
  }

  /**
   * Returns the value
   * 
   * @return mixed
   */
  public function value() {
    return $this->value;
  }

  /**
   * Returns the parent page
   * 
   * @return object KirbyPage
   */
  public function page() {
    return $this->file->page();
  }

  /**
   * Returns the parent content file
   * 
   * @return object KirbyContent
   */
  public function file() {
    return $this->file;
  }

  /**
   * Checks if the value is empty
   * 
   * @return boolean
   */
  public function isEmpty() {
    return empty($this->value);
  }

  /**
   * Converts the entire object to a string
   * Simply used to echo the variable
   * 
   * @return string
   */
  public function __toString() {
    return (string)$this->value;
  }

}