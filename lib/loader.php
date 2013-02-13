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
   * Loads the configuration for the site
   * including the environment based config
   * (detected by host name)
   * 
   * @param array $config Additional config variables to merge into the loaded configuration
   */
  public function config($late = array()) {
    
    // the root of all custom config files
    $root = c::get('root.config');
    
    // load custom config files
    $this->file($root . DS . 'config.php');
    $this->file($root . DS . 'config.' . server::get('server_name') . '.php');

    // get all config options that have been stored so far
    $defaults = c::get();

    // merge them with the passed late options again
    $config = array_merge($defaults, $late);

    // store them again
    c::set($config);

  }

  /**
   * Loads all available plugins
   * 
   * @param string $folder This is used to recursively search for plugins in subfolders
   */
  public function plugins($folder=false) {

    $root   = c::get('root.plugins');
    $folder = ($folder) ? $folder : $root;
    $files  = dir::read($folder);

    if(!is_array($files)) return false;
    
    foreach($files as $file) {
      
      if(is_dir($folder . DS . $file) && $folder == $root) {
        $this->plugins($folder . DS . $file);
        continue;
      }
        
      if(f::extension($file) != 'php') continue;
      $this->file($folder . DS . $file);

    }

  }

  /**
   * Loads all text parsers
   */
  public function parsers() {
    
    $root = c::get('root.parsers');

    include($root . DS . 'yaml.php');
    include($root . DS . 'kirbytext.php');
    include($root . DS . 'smartypants.php');
    include($root . DS . 'shortcuts.php');

    if(c::get('markdown.extra')) {
      include($root . DS . 'markdown.extra.php');
    } else {
      include($root . DS . 'markdown.php');    
    }
    
  }

  /**
   * Loads custom language files
   */
  public function language() {
    $root    = c::get('root.languages');
    $default = $root . DS . c::get('lang.default') . '.php';    
    $current = $root . DS . c::get('lang.current') . '.php';    
    
    $this->file($default);
    $this->file($current);
  }

  /**
   * Loads the given file if it exists
   */
  public function file($file) {
    if(!file_exists($file)) return false;
    require($file);    
  }

}