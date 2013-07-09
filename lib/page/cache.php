<?php

namespace Kirby\CMS\Page;

use Kirby\Toolkit\C;
use Kirby\CMS\Page;
use Kirby\CMS\Site;

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Page Cache
 * Caches the full rendered HTML of a page
 * 
 * @package   Kirby CMS
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://getkirby.com/license
 */
class Cache {

  // The cached Page object
  protected $page = null;

  // The cache file id  
  protected $id = null;
  
  // The cache data if available
  protected $data = null;
    
  // Is the data cache enabled at all?
  protected $enabled = null;

  /**
   * Constructor
   * 
   * @param object Site
   * @param object Page
   */
  public function __construct(Page $page) {
  
    $this->page    = $page;
    $this->id      = 'html' . DS . $this->page->id();
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
   * Checks if the url or template should be ignored
   * 
   * @return boolean
   */
  public function isIgnored() {

    $url      = $this->page->uri();
    $template = $this->page->template();

    // get all templates that shuold be ignored
    $templates = c::get('cache.ignore.templates');

    // ignore all pages with one of the templates from above
    if(in_array($template, $templates)) return true;

    // get all urls that shuold be ignored, with a fallback for the old format
    $urls = c::get('cache.ignore.urls', c::get('cache.ignore'));

    foreach($urls as $pattern) {

      if(($pattern == '/' || $pattern == c::get('home')) && in_array($url, array(c::get('home'), '/', ''))) return true;

      $regex = '!^' . $pattern . '$!i';
      $regex = str_replace('*', '.*?', $regex);

      if(preg_match($regex, $url)) return true;

    }        

    return false;

  }
  
  /**
   * Checks if there is a valid cache file available
   * 
   * @return boolean
   */
  public function isAvailable() {
    return $this->isEnabled() && \Kirby\Toolkit\Cache::created($this->id) >= site::instance()->modified() ? true : false;
  }

  /**
   * Tries to get the data from cache
   * 
   * @return mixed 
   */
  public function get() {

    if(!$this->isAvailable() || $this->isIgnored()) return false;

    return $this->data = \Kirby\Toolkit\Cache::get($this->id);    
  
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
      \Kirby\Toolkit\Dir::make(KIRBY_SITE_ROOT_CACHE . DS . 'html', $recursive = true);

      // store the cache file 
      \Kirby\Toolkit\Cache::set($this->id, $this->data);

    }

  }

}