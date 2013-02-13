<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Request
 * 
 * The request object contains all info
 * and data about incoming GET, POST, PUT & DELETE 
 * requests. 
 * 
 * @package Kirby CMS
 */
class KirbyRequest {

  // the incoming data array
  protected $data = null;
  
  // the used request method
  protected $method = null;  
  
  // the request body
  protected $body = null;

  /**
   * Constructor
   */
  public function __construct() {
    $this->data();  
  }

  /**
   * Returns either the entire data array or parts of it
   * 
   * @param string $key An optional key to receive only parts of the data array
   * @param mixed $default A default value, which will be returned if nothing can be found for a given key
   * @param mixed
   */
  public function data($key = null, $default = null) {
    
    if(!is_null($this->data)) {
      $data = $this->data;
    } else {
      $_REQUEST = array_merge($_GET, $_POST);
      $data = $this->data = ($this->is('GET')) ? $this->sanitize($_REQUEST) : array_merge($this->body(), $this->sanitize($_REQUEST));
    }
    
    if(is_null($key)) return $data;

    return isset($data[$key]) ? $data[$key] : $default;
    
  }

  /**
   * Alternative to self::date($key, $default)
   * 
   * @param string $key An optional key to receive only parts of the data array
   * @param mixed $default A default value, which will be returned if nothing can be found for a given key
   * @param mixed
   */
  public function get($key = null, $default = null) {
    return $this->data($key, $default);  
  }

  /**
   * Sets or overwrites a variable in the data array
   * 
   * @param mixed $key The key to set/replace. Use an array to set multiple values at once
   * @param mixed $value The value
   * @return object $this
   */
  public function set($key, $value = null) {
    if(is_array($key)) {
      foreach($key as $k => $v) {
        $this->set($k, $v);
      }
      return $this;
    }
    $this->data[$key] = $this->sanitize($value);
    return $this;
  }

  /**
   * Returns the sanitized request body
   * 
   * @return array
   */
  public function body() {
    if(!is_null($this->body)) return $this->body; 
    @parse_str(@file_get_contents('php://input'), $this->body); 
    return $this->body = $this->sanitize((array)$this->body);
  }
  
  /**
   * Returns the used request method (GET, POST, PUT, DELETE)
   * 
   * @return string
   */
  public function method() {
    if(!is_null($this->method)) return $this->method;
    return $this->method = strtoupper($_SERVER['REQUEST_METHOD']);
  }

  /**
   * Checks if the request is of a specific type: 
   * 
   * - GET
   * - POST
   * - PUT
   * - DELETE
   * - AJAX
   * 
   * @return boolean
   */
  public function is($method) {
    if($method == 'ajax') {
      return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ? true : false;    
    } else {
      return (strtoupper($method) == $this->method()) ? true : false;
    }
  }

  /**
   * Returns the referer if available
   * 
   * @param string $default Pass an optional URL to use as default referer if no referer is being found
   * @return string
   */
  public function referer($default = '/') {
    return !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $default;
  }

  /**
   * Nobody remembers how to spell it
   * so this is a shortcut
   * 
   * @param string $default Pass an optional URL to use as default referer if no referer is being found
   * @return string
   */
  public function referrer($default = '/') {
    return $this->referer($default);    
  }

  /**
   * Returns the IP address from the 
   * request user if available
   * 
   * @param mixed
   */
  public function ip() {
    return !empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : false;
  }
  
  /**
   * Private method to sanitize incoming request data
   * 
   * @param array $data
   * @return array 
   */
  protected function sanitize($data) {

    if(!is_array($data)) {
      return trim(str::stripslashes($data));      
    }

    foreach($data as $key => $value) {
      $value = $this->sanitize($value);
      $data[$key] = $value;    
    }      
    return $data;  

  }
 
}