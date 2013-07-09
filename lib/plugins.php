<?php 

namespace Kirby\CMS;

use Kirby\Toolkit\A;
use Kirby\Toolkit\Dir;

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Plugins
 * 
 * The Plugins class provides a list of all installed
 * plugins and is also responsible to load and install them. 
 * 
 * @package   Kirby CMS
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://getkirby.com/license
 */
class Plugins {

  // a list of all installed plugins
  protected $plugins = array();

  /**
   * Loads either a single or all available plugins
   * 
   * @param string $id Optional plugin id (folder name) to install a single plugin
   */
  public function load($id = null) {

    // load a specific plugin
    if(is_null($id)) {

      $dirs = array_merge(dir::read(KIRBY_CMS_ROOT_PLUGINS), dir::read(KIRBY_SITE_ROOT_PLUGINS));

      foreach($dirs as $id) $this->load($id);
      return true;

    }

    $root = KIRBY_SITE_ROOT_PLUGINS . DS . $id;

    // fall back to core plugins
    if(!file_exists($root)) $root = KIRBY_CMS_ROOT_PLUGINS . DS . $id;
    
    if(!is_dir($root)) return false;
    
    $this->install($id, $root);

  }

  /**
   * Installs a plugin by id and root
   * 
   * @param string $id id/folder name of a plugin
   * @param string $root The full root to the plugin folder
   */
  public function install($id, $root) {

    if(isset($this->plugins[$id])) return false;
    
    $this->plugins[$id] = Plugin::install($id, $root);

  }

  /**
   * Checks if a plugin is available by its folder name
   * 
   * @param string $id id/folder name of a plugin
   * @return boolean
   */
  public function has($id) {
    return isset($this->plugins[$id]);
  }

  /**
   * Makes it possible to get a plugin by name 
   * with a smart getter method 
   * 
   * @param string $id id/folder name of a plugin
   * @param array $arguments Additional arguments to pass to the plugin (optional)
   * @return object Returns the Plugin object if such a plugin is installed
   */
  public function __call($id, $arguments = null) {

    // try to lazy load the plugin if it's not available yet
    if(!isset($this->plugins[$id])) {
      $this->load($id);
    } 
    
    return a::get($this->plugins, $id);

  }

  /**
   * Returns a more readable dump array for the dump() helper
   * 
   * @return array
   */
  public function __toDump() {

    $plugins = array();

    foreach($this->plugins as $plugin) {
      $plugins[$plugin->id()] = $plugin->__toDump();
    }

    return $plugins;

  }

}