<?php 

namespace Kirby\CMS;

use Kirby\Toolkit\F;
use Kirby\Toolkit\Object;

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Plugin
 * 
 * Represents a single Kirby plugin
 * New plugins should all extend this class to gain
 * handy methods like clean initializing and loading of sub files. 
 * 
 * @package   Kirby CMS
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://getkirby.com/license
 */
class Plugin {

  // the full root of the plugin folder
  protected $root;

  // a Object with available info from a package.json file
  protected $info;

  // the name of the plugin
  protected $name;
  
  // the id of the plugin, which is also the folder name
  protected $id;
  
  // the plugin description, coming from the package.json file
  protected $description;
  
  /**
   * Installs a plugin and also initializes a child class of 
   * Plugin if available. 
   * 
   * @param string $id id/folder name of this plugin
   * @param string $root the full root to the plugin folder
   */
  static public function install($id, $root) {

    $file  = $root . DS . $id . '.php';
    $class = $id . 'Plugin';

    // try to load the main file
    if(file_exists($file)) include_once($file);
    
    // if a child class is available, use that. otherwise use the Plugin mother class 
    return (class_exists($class)) ? new $class($id, $root) : new self($id, $root);

  }

  /**
   * Constructor
   * 
   * Don't overwrite the constructor in child classes
   * Use the onInstall event method instead
   * 
   * @param string $id id/folder name of this plugin
   * @param string $root the full root to the plugin folder
   */
  public function __construct($id, $root) {
    $this->id   = $id;
    $this->root = $root;
  }

  /**
   * Returns a Object object with all info from 
   * the package.json if available
   * 
   * @return object Object
   */
  public function info() {
    if(!is_null($this->info)) return $this->info;
    return $this->info = new Object(f::read($this->root . DS . 'package.json', 'json'));
  }

  /**
   * Returns the id/folder name of the plugin
   * 
   * @return string
   */
  public function id() {
    return $this->id;
  }

  /**
   * Returns the root location of the plugin
   * 
   * @return string
   */
  public function root() {
    return $this->root;
  }

  /**
   * Returns the name name of the plugin
   * 
   * @return string
   */
  public function name() {
    if(!is_null($this->name)) return $this->name;    
    $name = $this->info()->name();
    return $this->name = ($name != '') ? $name : $this->id();   
  }

  /**
   * Returns the version number of the plugin
   * 
   * @return float
   */
  public function version() {
    return $this->info()->version();
  }

  /**
   * Renders some helpful information about
   * the plugin when you try to echo it. 
   * 
   * @return string
   */
  public function __toString() {

    $html = array();

    $html[] = 'Plugin: ' . $this->name();
    $html[] = 'Description: ' . $this->info()->description();
    $html[] = 'Version: ' . $this->version();
    $html[] = 'Author: ' . $this->info()->author();
    $html[] = 'URL: ' . $this->info()->url();

    return '<pre>' . implode('<br />', $html) . '</pre>';

  }

  /**
   * Returns a more readable dump array for the dump() helper
   * 
   * @return array
   */
  public function __toDump() {

    return array(
      'id'          => $this->id(),
      'name'        => $this->name(),
      'root'        => str_replace(KIRBY_INDEX_ROOT, '', $this->root()),
      'description' => $this->info()->description(),
      'version'     => $this->version(), 
      'author'      => $this->info()->author(),
      'url'         => $this->info()->url(),
    );

  }

}