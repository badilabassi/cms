<?php 

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Dir
 *
 * This object represents a content directory. 
 * It's used within the KirbyPage object to provide
 * additional info and methods for a directory of a page
 * 
 * @package Kirby CMS
 */
class KirbyDir {

  // the full root path of the directory 
  protected $root = null;

  // the name of the directory
  protected $name = null;

  // the unique id of the directory (without the prepended number)
  protected $uid  = null;

  // the uri - relative to the main content directory
  protected $uri  = null;

  // all files within this directory
  protected $files = null;

  // all subdirs within this directory
  protected $children = null;

  /**
   * Constructor
   * 
   * @param string $root The full root of the directory. Normally passed by the parent page object
   */
  public function __construct($root) {
    $this->root = $root; 
    $this->name = basename($this->root);
    
    // extract the uid and num of the directory
    if(preg_match('/^([0-9]+[\-]+)/', $this->name, $match)) {
      $this->uid = str_replace($match[1], '', $this->name);
      $this->num = trim(rtrim($match[1], '-'));
    } else {
      $this->num = null;
      $this->uid = $this->name;
    }
      
  }

  /**
   * Returns the full directory path 
   * 
   * @return string 
   */
  public function root() {
    return $this->root;
  }

  /**
   * Returns the base directory name 
   * 
   * @return string 
   */
  public function name() {
    return $this->name;
  }

  /** 
   * Returns the optional prepended
   * sorting number from the folder name 
   * 
   * @return string i.e. 01-projects returns 01
   */
  public function num() {
    return $this->num;
  }

  /** 
   * Returns the unique id
   * The unique id is the folder name without 
   * prepended sorting number.
   * 
   * @return string i.e. 01-projects returns projects
   */
  public function uid() {
    return $this->uid;
  }

  /**
   * Returns the relative directory path
   * excluding the document root
   * 
   * @return string i.e. content/01-projects
   */
  public function uri() {
    
    if(!is_null($this->uri)) return $this->uri;

    if(ROOT == DS) {
      $this->uri = ltrim($this->root, DS);
    } else {
      $this->uri = ltrim(str_replace(ROOT, '', $this->root), DS);
    }

    $this->uri = str_replace(DS, '/', $this->uri);
    return $this->uri;

  }

  /**
   * Scans all contents of this directory
   * and divides items into dirs and files 
   */
  private function scan() {

    // dependencies
    require_once(ROOT_KIRBY_LIB . DS . 'cache' . DS . 'structure.php');

    $this->files    = array();
    $this->children = array();
    
    $ignore = array('.svn', '.git', '.htaccess', '.', '..', '.DS_Store');
    $ignore = array_merge($ignore, (array)c::get('content.file.ignore', array()));
    $all    = array_diff(scandir($this->root), $ignore);

    foreach($all as $file) {
      $item = $this->root . DS . $file;

      if(is_dir($item)) {
        $this->children[$file] = $item;      
      } else {
        $this->files[$file] = $item;            
      }

    }

  }

  /**
   * Returns an array with all files within this directory
   *
   * @return array
   */
  public function files() {
    if(is_null($this->files)) $this->scan();
    return $this->files;
  }

  /**
   * Returns an array with all subdirectories within this directory
   *
   * @return array
   */
  public function children() {
    if(is_null($this->children)) $this->scan();
    return $this->children;
  }

  /**
   * Returns the unix timestamp of the last modification 
   * date of the directory. This can be used for cache invalidation.
   * 
   * @return int
   */
  public function modified() {
    return filectime($this->root);
  }

  /**
   * Checks if the directory is readable
   * 
   * @return boolean
   */  
  public function isReadable() {
    return is_readable($this->root);
  }

  /**
   * Checks if the directory is writable
   * 
   * @return boolean
   */  
  public function isWritable() {
    return is_writable($this->root);
  }

  /**
   * Returns a md5 hash of the root 
   */
  public function hash() {
    return md5($this->root);
  }

  /**
   * Echos the root of the directory if the entire object is echoed. 
   * 
   * @return string
   */
  public function __toString() {
    return $this->root;  
  }

}
