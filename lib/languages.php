<?php 

namespace Kirby\CMS;

use Kirby\Toolkit\C;
use Kirby\Toolkit\Collection;
use Kirby\Toolkit\URI;

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Languages
 * 
 * A collection of all available languages for multi-language sites.
 * 
 * @package   Kirby CMS
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://getkirby.com/license
 */
class Languages extends Collection {

  /**
   * Constructor
   * 
   * @param array $codes An optional array of available language codes
   */
  public function __construct($codes = array()) {

    if(empty($codes)) $codes = c::get('lang.available', array());

    // get the uri including the language code
    $uri    = new uri(null, array('subfolder' => site::instance()->subfolder()));
    $active = (c::get('lang.current') && in_array(c::get('lang.current'), $codes)) ? c::get('lang.current') : $uri->path()->first();

    // if there's no code available in the url, use the default language
    if(empty($active) || !in_array($active, c::get('lang.available'))) $active = c::get('lang.default');

    // store the current language code in the config 
    c::set('lang.current', $active);

    // attach all languages
    foreach($codes as $lang) {
      $this->set($lang, new Language($lang, $lang == $active));
    }

  }  

  /**
   * Returns the currently active language
   * 
   * @return object Language
   */
  public function findActive() {
    foreach($this->data as $lang) {
      if($lang->isActive()) return $lang;
    }
  }

  /**
   * Returns the default language
   * 
   * @return object Language
   */
  public function findDefault() {
    foreach($this->data as $lang) {
      if($lang->isDefault()) return $lang;
    }
  }

  /**
   * Returns the user's preferred language
   * 
   * @return object Language
   */
  public function findPreferred() {
    foreach($this->data as $lang) {
      if($lang->isPreferred()) return $lang;
    }
    return $this->findDefault();
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

  /**
   * Returns a more readable dump array for the dump() helper
   * 
   * @return array
   */
  public function __toDump() {

    $languages = array();

    foreach($this->toArray() as $language) {
      $languages[$language->code()] = $language->__toDump();
    }

    return $languages;

  }

}