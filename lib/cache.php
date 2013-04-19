<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * HTML Cache
 * Caches the full rendered HTML of a page
 * 
 * @package Kirby CMS
 */
class KirbyCache {

  // The cache file id  
  protected $id = null;
  
  // The cache data if available
  protected $data = null;
  
  // The cached KirbyPage object
  protected $parent = null;
  
  // The KirbySite object
  protected $site = null;

  // Is the data cache enabled at all?
  protected $enabled = null;

  /**
   * Constructor
   * 
   * @param object KirbySite
   * @param object KirbyPage
   */
  public function __construct(KirbySite $site, KirbyPage $parent) {
  
    $this->site    = $site;  
    $this->parent  = $parent;
    $this->id      = 'html' . DS . $this->parent->dir()->hash();
    $this->enabled = (c::get('cache') && c::get('cache.html')) ? true : false;

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
   * 
   */
  public function isIgnored() {
    return cache::ignored($this->parent->uri(), $this->parent->template());
  }

  /**
   * Checks if there is a valid cache file available
   * 
   * @return boolean
   */
  public function isAvailable() {
    return $this->isEnabled() && cache::modified($this->id) >= $this->site->modified() ? true : false;
  }

  /**
   * Tries to get the data from cache
   * 
   * @return mixed 
   */
  public function get() {

    if(!$this->isAvailable() || $this->isIgnored()) return false;

    return $this->data = cache::get($this->id);    
  
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
  public function set($html) {

    $this->data = $html;

    if($this->isEnabled() && !$this->isIgnored()) {

      // make sure the directory is there
      dir::make(ROOT_SITE_CACHE . DS . 'html', $recursive = true);

      // store the cache file 
      cache::set($this->id, $this->data);

    }

  }

}