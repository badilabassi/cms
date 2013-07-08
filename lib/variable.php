<?php 

namespace Kirby\CMS;

use Kirby\CMS\File\Content;

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
 * @package   Kirby CMS
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://getkirby.com/license
 */
class Variable {

  // the key of this variable
  protected $key = null;
  
  // the value of this variable
  protected $value = null;

  // the parent Page object
  protected $page = null;

  // the parent ContentFile file object
  protected $file = null;

  /**
   * Constructor
   * 
   * @param string $key The name of the key for this variable
   * @param mixed $value The value for this variable
   * @param object $file The parent ContentFile object
   */
  public function __construct($key, $value, Content $file = null) {
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
   * @return object Page
   */
  public function page() {
    return $this->file->page();
  }

  /**
   * Returns the parent content file
   * 
   * @return object Content
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
   * Splits the value by the given delimiter
   * 
   * @param string $separator The delimiter to split by
   * @return array
   */
  public function split($separator = ',') {
    return str::split($this->value, $separator);
  }

  /**
   * Converts the value to a string
   * 
   * @return string
   */
  public function toString() {
    return (string)$this->value;
  }

  /**
   * Converts the value to secure HTML
   * 
   * @return string
   */
  public function toHtml() {
    return html($this->value);
  }

  /**
   * Converts the value to kirbytext
   * 
   * @return string
   */
  public function toKirbytext() {
    return kirbytext($this);
  }

  /**
   * Converts the value to a unix timestamp if possible
   * 
   * @return string
   */
  public function toTimestamp() {
    return strtotime($this->value);
  }

  /**
   * Converts the value to a formatted date
   *
   * @param string $format
   * @return string
   */
  public function toDate($format = 'Y-m-d H:i:s') {
    return date($format, $this->toTimestamp());
  }

  /**
   * Parses the value with the yaml parser to convert it to an array
   *
   * @return array
   */
  public function toArray() {
    return yaml($this->value);
  }

  /**
   * Applies the url() helper function to the value
   *
   * @return string
   */
  public function toUrl() {
    return url($this->value);
  }

  /**
   * Returns a page with the same uri as the value
   *
   * @return object
   */
  public function toPage() {
    return site::instance()->children()->find($this->value);
  }

  /**
   * Uses the yaml parser to convert the value to an array first
   * and returns the entire thing as json afterwards
   *
   * @return string
   */
  public function toJson() {
    return json_encode($this->toArray());
  }

  /**
   * Uses the yaml parser to convert the value to an array first
   * and converts it to a Collection object
   *
   * @return object
   */
  public function toCollection() {
    return new Collection($this->toArray());
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

  /**
   * Additional Shortcuts for all the methods above
   * 
   * @param string $name Name of the method will be passed by PHP
   * @param array $arguments an optional list of passed arguments
   * @return mixed
   */
  public function __call($name, $arguments) {

    $aliases = array(
      'h'          => 'toHTML',
      'html'       => 'toHTML',
      'kirbytext'  => 'toKirbytext',
      'kt'         => 'toKirbytext',
      'str'        => 'toString',
      'a'          => 'toArray', 
      'ts'         => 'toTimestamp',
      'timestamp'  => 'toTimestamp',
      'date'       => 'toDate',
      'collection' => 'toCollection',
      'json'       => 'toJson',
      'url'        => 'toURL',
    );

    if(array_key_exists($name, $aliases)) {
      return call_user_func_array(array($this, $aliases[$name]), $arguments);
    } else {
      raise('invalid method: ' . $name);
    }

  }

}