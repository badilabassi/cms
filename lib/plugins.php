<?php 

/**
 * Plugins
 * 
 * The KirbyPlugins class provides a list of all installed
 * plugins and is also responsible to load and install them. 
 * 
 * @package Kirby CMS
 */
class KirbyPlugins {

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

      $dirs = array_merge(dir::read(ROOT_KIRBY_PLUGINS), dir::read(ROOT_SITE_PLUGINS));

      foreach($dirs as $id) $this->load($id);
      return true;

    }

    $root = ROOT_SITE_PLUGINS . DS . $id;

    if(!file_exists($root)) {
      $root = ROOT_KIRBY_PLUGINS . DS . $id;
    }

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
    
    $this->plugins[$id] = KirbyPlugin::install($id, $root);

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
   * @return object Returns the KirbyPlugin object if such a plugin is installed
   */
  public function __call($id, $arguments = null) {

    // try to lazy load the plugin if it's not available yet
    if(!isset($this->plugins[$id])) {
      $this->load($id);
    } 
    
    return a::get($this->plugins, $id);

  }

}