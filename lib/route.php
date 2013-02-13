<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Route
 * 
 * A route can be added to the KirbyRouter
 * which wil then try to match it agains the current URL
 * 
 * @package Kirby CMS
 */
class KirbyRoute {

  // the url pattern for this route
  protected $url = null;
  
  // the allowed methods
  protected $methods = null;
  
  // the uri for the page object, which should be used
  protected $page = null;

  // an array of params, which are found in the url 
  protected $params = array();

  /**
   * Constructor
   * 
   * @param string $url The url pattern for this route
   * @param array $params Additional params for the route (allowed methods and the page uri)
   */
  public function __construct($url, $params = array()) {

    $defaults = array(
      'methods' => array('GET', 'POST', 'DELETE', 'PUT'), 
      'page'    => null
    );

    $options = array_merge($defaults, $params);

    $this->url     = $url;
    $this->methods = is_array($options['methods']) ? $options['methods'] : array($options['methods']);
    $this->page    = $options['page'];

  }

  /**
   * Returns the url pattern
   * 
   * @return string
   */
  public function url() {
    return $this->url;
  }

  /**
   * Returns the allowed methods
   * 
   * @return array
   */
  public function methods() {
    return $this->methods;
  }

  /**
   * Returns the page object for this route if available
   * 
   * @return object KirbyPage
   */
  public function page() {
    
    if(is_null($this->page)) return false;

    $url = $this->page;

    foreach($this->params as $key => $value) {
      $url = str_replace('@' . $key, $value, $url);
    }

    return site()->pages()->find($url);
  
  }

  /**
   * Returns the array of params, which are found in the url
   * 
   * @param string $key An optional key to receive only a part of the params array
   * @param string $value If a key and a value are being passed, this is used as a setter
   * @return mixed
   */
  public function params($key = null, $value = null) {
    if(is_null($key)) return $this->params;
    if(is_null($value)) return a::get($this->params, $key);
    $this->params[$key] = $value; 
  }

}