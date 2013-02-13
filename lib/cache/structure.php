<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Structure Cache
 * Caches the files and directories within content folders
 * 
 * @package Kirby CMS
 */
class KirbyStructureCache {

  // The cache file id  
  protected $id = null;
  
  // The cache data if available
  protected $data = null;
  
  // The cached KirbyDir object
  protected $dir = null;
  
  // Is the structure cache enabled at all?
  protected $enabled = null;

  /**
   * Constructor
   * 
   * @param object KirbyDir
   */
  public function __construct(KirbyDir $dir) {
    
    $this->dir     = $dir;
    $this->id      = 'structure/' . $this->dir->hash();
    $this->enabled = c::get('cache.structure');

    // make sure the directory is there
    dir::make(cache::file('structure'));

  }

  /**
   * Checks if the structure cache is enabled
   * 
   * @return boolean
   */
  public function isEnabled() {
    return $this->enabled;
  }

  /**
   * Checks if there is a valid cache file available
   * 
   * @return boolean
   */
  public function isAvailable() {
    return $this->isEnabled() && cache::modified($this->id) >= $this->dir->modified() ? true : false;
  }

  /**
   * Tries to get the data from cache
   * 
   * @return mixed 
   */
  public function get() {

    if(!$this->isAvailable()) return false;

    $this->data = (array)cache::get($this->id);    
    
    if(!isset($this->data['files']) || !isset($this->data['children'])) return $this->data = null;

    return $this->data;
  
  }

  /**
   * Returns the cached data
   * 
   * @return array
   */
  public function data() {
    return $this->data;
  }

  /**
   * Set a fresh cache file if enabled
   */
  public function set() {

    $this->data = array(
      'files'    => $this->dir->files(), 
      'children' => $this->dir->children(), 
    );

    if($this->isEnabled()) {
      cache::set($this->id, $this->data);
    }

  }

}
