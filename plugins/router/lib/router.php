<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Router
 * 
 * The router makes it possible to redirect
 * any URL to different locations/pages
 * 
 * @package Kirby CMS
 */
class KirbyRouter {

  // an array of added routes
  protected $routes = array();
  
  // the parent site object
  protected $site = null;

  // found arguments
  protected $args = array();

  // found route
  protected $route = null;

  /**
   * Constructor
   * 
   * @param object The parent KirbySite object
   */
  public function __construct(KirbySite $site) {
    $this->site = $site;
  }

  /**
   * Adds a new route
   * 
   * @param string $url The url pattern, which should be matched
   * @param array $params An array of parameters for this route
   * @return object $this
   */
  public function add($url, $params = array()) {

    if(!is_array($params)) $params = array(
      'page' => $params
    );

    $this->routes[$url] = new KirbyRoute($url, $params);
    return $this;
  }

  /**
   * Returns all added routes
   * 
   * @return array
   */
  public function routes() {
    return $this->routes;
  }

  /**
   * Returns the found route if available
   * 
   * @return object KirbyRoute
   */
  public function route() {
    return $this->route;    
  }

  /**
   * Returns all matched parameters from the active Route
   * 
   * @param string $key An optional key to receive only a part of the params array
   * @param string $value If a key and a value are being passed, this is used as a setter
   * @return mixed
   */
  public function params($key = null, $value = null) {
    return ($this->route) ? $this->route->params($key, $value) : array();
  }

  /**
   * Goes through all available routes and tries
   * to match the current URL with one of the route patterns
   *
   * @return mixed KirbyRoute or false
   */
  public function resolve() {

    $path   = $this->site->uri()->path()->toString();
    $method = $this->site->request()->method();
    
    foreach($this->routes as $key => $route) {  
      
      if(!in_array($method, $route->methods())) continue;
      
      $char  = substr($key, -1);
      $key   = str_replace(')', ')?', $key);
      $regex = preg_replace_callback('#@([\w]+)(:([^/\(\)]*))?#', array($this, 'match'), $key);

      switch($char) {
        // fix trailing slash
        case '/':
          $regex .= '?';
          break;
        // enable wildcard
        case '*':
          $regex = str_replace('*', '.+?', $key);
          break;
        default:
          $regex .= '/?';
          break;
      }

      if(preg_match('#^'.$regex.'(?:\?.*)?$#i', $path, $matches)) {

        foreach($this->args as $k => $v) {
          $route->params($k, (array_key_exists($k, $matches)) ? urldecode($matches[$k]) : null);
        }

        // the route is only valid if the attached page exists
        if($route && $route->page()) return $this->route = $route;
          
      }

    }
    
    return false;    

  }

  /**
   * Callback for the preg_replace_callback function in self::resolve()
   * 
   * @param array $matches
   * @return string
   */
  protected function match($matches) {

    $this->args[$matches[1]] = null;
    if(isset($matches[3])) {
      return '(?P<'.$matches[1].'>'.$matches[3].')';
    }
    return '(?P<'.$matches[1].'>[^/\?]+)';

  }

}