<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Template
 *
 * This is Kirby's super minimalistic 
 * template engine. It loads and fills 
 * templates. Who would have thought that
 * 
 * @package Kirby CMS
 */
class KirbyTemplate {

  // all global variables which will be passed to the templates
  static public $vars = array();

  /**
   * Sets a new template variable
   * 
   * @param string $key
   * @param string $value
   */
  static public function set($key, $value=false) {
    if(is_array($key)) {
      self::$vars = array_merge(self::$vars, $key);
    } else {
      self::$vars[$key] = $value;
    }
  }

  /**
   * Returns a template variable by key
   * 
   * @param string $key
   * @param string $default
   * @return mixed
   */
  static public function get($key=null, $default=null) {
    if($key===null) return (array)self::$vars;
    return a::get(self::$vars, $key, $default);       
  }

  /**
   * Loads a template and returns its output
   * 
   * @param string $template The name of the template. The template must be located within the template folder (root.templates)
   * @param string $vars Additional template vars to pass to the template
   * @param boolean $return true: html will be returned, false: html will be echoed directly
   * @return string
   */
  static public function load($template='default', $vars=array(), $return=false) {    
    $file = c::get('root.templates') . '/' . $template . '.php';
    return self::loadFile($file, $vars, $return);
  }
  
  /**
   * Loads a specific template file and returns its output
   * 
   * @param string $file The full root to the template file
   * @param string $vars Additional template vars to pass to the template
   * @param boolean $return true: html will be returned, false: html will be echoed directly
   * @return string
   */
  static public function loadFile($file, $vars=array(), $return=false) {
    if(!file_exists($file)) return false;
    @extract(self::$vars);
    @extract($vars);
    content::start();
    require($file);
    return content::end($return); 
  }

}
