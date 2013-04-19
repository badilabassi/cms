<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Visitor
 * 
 * @package Kirby CMS
 */
class KirbyVisitor extends Visitor {

  // cache for the detected language
  protected $language = null;

  /**
   * Returns the user's accepted language code
   * 
   * @return string
   */
  public function acceptedLanguageCode() {
    if(!is_null(self::$acceptedLanguageCode)) return self::$acceptedLanguageCode;
    $detected = parent::acceptedLanguageCode();
    $detected = (empty($detected) || !in_array($detected, c::get('lang.available'))) ? c::get('lang.default') : $detected;    
    return self::$acceptedLanguageCode = $detected;
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

}