<?php 

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

// dependencies
require_once('collection.php');
require_once('language.php');

/**
 * Languages
 * 
 * @package Kirby CMS
 */
class KirbyLanguages extends KirbyCollection {

  /**
   * Constructor
   * 
   * @param array $codes An optional array of available language codes
   */
  public function __construct($codes = array()) {

    if(empty($codes)) $codes = c::get('lang.available', array());

    foreach($codes as $lang) {
      $this->set($lang, new KirbyLanguage($lang));
    }

  }  

  /**
   * Returns the currently active language
   * 
   * @return object KirbyLanguage
   */
  public function findActive() {
    foreach($this->_ as $lang) {
      if($lang->isActive()) return $lang;
    }
  }

  /**
   * Returns the default language
   * 
   * @return object KirbyLanguage
   */
  public function findDefault() {
    foreach($this->_ as $lang) {
      if($lang->isDefault()) return $lang;
    }
  }

  /**
   * Returns the user's preferred language
   * 
   * @return object KirbyLanguage
   */
  public function findPreferred() {
    foreach($this->_ as $lang) {
      if($lang->isPreferred()) return $lang;
    }
  }

  /**
   * Converts the current object to a string
   * This will return all urls for all languages
   *
   * @return string 
   */
  public function __toString() {
    $output = array();
    foreach($this->toArray() as $key => $lang) {
      $output[] = '<a href="' . $lang->url() . '">' . $lang->url() . '</a><br />';          
    }    
    return implode(PHP_EOL, $output);
  }

}