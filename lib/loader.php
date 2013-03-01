<?php 

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Component Loader
 * This is used in the KirbySite object
 * to load additonal components and plugins
 *
 * @package Kirby CMS 
 */
class KirbyLoader {

  /**
   * Loads all text parsers
   */
  static public function parsers() {
  
    require_once(ROOT_KIRBY_PARSERS . DS . 'yaml.php');
    require_once(ROOT_KIRBY_PARSERS . DS . 'smartypants.php');

    if(c::get('markdown.extra')) {
      require_once(ROOT_KIRBY_PARSERS . DS . 'markdown.extra.php');
    } else {
      require_once(ROOT_KIRBY_PARSERS . DS . 'markdown.php');    
    }
    
  }

  /**
   * Loads custom language files
   */
  static public function language() {
    self::file(ROOT_SITE_LANGUAGES . DS . c::get('lang.default') . '.php');
    self::file(ROOT_SITE_LANGUAGES . DS . c::get('lang.current') . '.php');
  }

  /**
   * Loads the given file if it exists
   */
  static public function file($file) {
    if(!file_exists($file)) return false;
    require($file);    
  }

}