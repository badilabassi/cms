<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Cache
 *
 * Global cache class, which is used for structure
 * and html cache and can be used for other file-based
 * caches by plugins as well. 
 * 
 * @package Kirby CMS
 */
class Cache {
  
  /** 
   * Returns the full path for a cache file
   * 
   * @param   string  $file The filename
   * @return  string  Returns the full path
   */
  static function file($file) {
    return ROOT_SITE_CACHE . DS . $file;
  }
  
  /**
    * Writes a new cache file  
    * 
    * @param  string  $file The filename for the cache file
    * @param  mixed   $content The cached content. Can be a string, obj or array
    * @param  boolean $raw set this to true to switch off serialization
    */
  static function set($file, $content, $raw=false) {
    if(!c::get('cache')) return false;
    if($raw == false) $content = @serialize($content);
    if($content) f::write(self::file($file), $content);      
  }
  
  /** 
    * Gets cached content and checks for expired cache files
    * 
    * @param  string  $file The filename for the cache file
    * @param  boolean $raw Set to true to get the content without unserialize
    * @param  boolean $expires number of max seconds for the age of this cache 
    * @return mixed   The cached content or false if the cache expired
    */  
  static function get($file, $raw=false, $expires=false) {
    if(!c::get('cache')) return false;
    
    // check for an expired cache 
    if($expires && self::expired($file, $expires)) return false;

    $content = f::read(self::file($file));
    if($raw == false) $content = @unserialize($content);
    return $content;
  }  

  /**
    * Removes a file from the cache
    * 
    * @param  string  @file The filename for the cache file
    */  
  static function remove($file) {
    f::remove(self::$file);
  }

  /**
    * Removes all files from the cache directory
    */ 
  static function flush() {
    if(!is_dir(ROOT_SITE_CACHE)) return ROOT_SITE_CACHE;
    dir::clean(ROOT_SITE_CACHE);  
  }
  
  /** 
    * Checks when a cache file has been modified for the last time
    * 
    * @param  string  $file The name of the cache file
    * @return mixed   Returns the timestamp of false, if the file could not be found
    */
  static function modified($file) {
    if(!c::get('cache')) return false;
    return @filectime(self::file($file));
  }
  
  /** 
    * Checks if a cached file expired
    * 
    * @param  string  $file The name of the cache file
    * @param  int     $time The max age of the cache file in seconds (false=auto-check by modified date)
    * @return boolean  true: cache expired, false: cache is still valid
    */
  static function expired($file, $time=false) {
    return (cache::modified($file) < time()-$time) ? true : false;
  }

  /**
   * Checks if the url or template should be ignored
   * 
   * @param string $url the uri string of the current page
   * @param string $template the template name (without .php)
   * @return boolean
   */
  static function ignored($url, $template) {

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

}
