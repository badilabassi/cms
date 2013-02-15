<?php 

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Visitor
 * 
 * @package Kirby CMS
 */
class KirbyVisitor {

  // cache for the ip address
  protected $ip = null;
  
  // cache for the user agent string
  protected $ua = null;
  
  // cache for the detected language
  protected $language = null;

  /**
   * Returns the ip address of the current visitor
   * 
   * @return string
   */
  public function ip() {
    if(!is_null($this->ip)) return $this->ip;
    return $this->ip = site()->request()->ip();
  }

  /**
   * Returns the user agent string of the current visitor
   * 
   * @return string
   */
  public function ua() {
    if(!is_null($this->ua)) return $this->ua;
    return $this->ua = server::get('HTTP_USER_AGENT');
  }

  /**
   * A more readable but longer alternative for ua()
   * 
   * @return string
   */
  public function userAgent() {
    return $this->ua();
  }

  /**
   * Returns the user's accepted language
   * 
   * @return string
   */
  public function acceptedLanguage() {
    return server::get('http_accept_language');
  }

  /**
   * Returns the user's accepted language code
   * 
   * @return string
   */
  public function acceptedLanguageCode() {
    $detected = str::split($this->acceptedLanguage(), '-');
    $detected = str::lower(str::trim(a::first($detected)));
    return (empty($detected) || !in_array($detected, c::get('lang.available'))) ? c::get('lang.default') : $detected;    
  }

  /**
   * Returns the preferred language of the visitor
   * If no preferred language is found, the 
   * default language will be returned
   * 
   * @return object KirbyLanguage
   */
  public function language() {
  
    if(!is_null($this->language)) return $this->language;   
 
    // try to find the language in the available languages collection
    $language = site()->languages()->find($this->acceptedLanguageCode());

    // otherwise replace it with the default language
    if(!$language) $language = site()->languages()->findDefault();

    // store and return
    return $this->language = $language;
  
  }

  /**
   * Returns the referrer if available
   * 
   * @return string
   */
  public function referrer() {
    return site()->request()->referer();
  }

  /**
   * Nobody can remember if it is written with on or two r
   * 
   * @return string
   */
  public function referer() {
    return site()->request()->referer();
  }

  /**
   * Returns the ip address if the object 
   * is being converted to a string or echoed
   *
   * @return string
   */
  public function __toString() {
    return $this->ip();
  }

}