<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

// dependencies
require_once(dirname(__DIR__) . DS . 'cache.php');

/**
 * Data Cache
 * Caches the parsed data from content text files
 * 
 * @package Kirby CMS
 */
class KirbyDataCache {

  // The cache file id  
  protected $id = null;
  
  // The cache data if available
  protected $data = null;
  
  // The cached KirbyContent object
  protected $parent = null;
  
  // Is the data cache enabled at all?
  protected $enabled = null;

  /**
   * Constructor
   * 
   * @param object KirbyContent
   */
  public function __construct(KirbyContent $parent) {
    
    $this->parent  = $parent;
    $this->id      = 'data/' . $this->parent->hash();
    $this->enabled = c::get('cache.data');

    // make sure the directory is there
    dir::make(cache::file('data'));

  }

  /**
   * Checks if the data cache is enabled
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
    return $this->isEnabled() && cache::modified($this->id) >= $this->parent->modified() ? true : false;
  }

  /**
   * Tries to get the data from cache
   * 
   * @return mixed 
   */
  public function get() {

    if(!$this->isAvailable()) return false;

    return $this->data = (array)cache::get($this->id);    
  
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

    $this->data = $this->parent->data();

    if($this->isEnabled()) {
      cache::set($this->id, $this->data);
    }

  }

}
