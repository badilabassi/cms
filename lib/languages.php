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

  protected $default   = null;
  protected $preferred = null;
  protected $current   = null;
  protected $codes     = null;

  /**
   * Constructor
   * 
   * @param array $codes An optional array of available language codes
   */
  public function __construct() {

    // get all available languages
    $languages = c::get('lang.config', array());

    if(empty($languages)) raise('Invalid language setup. Please refer to the docs');

    // setup all available languages
    foreach($languages as $config) {
      $this->set($config['code'], new Language($config));
    }

    // check for a current language code in the config
    // this will overwrite codes in the url
    $currentCode = c::get('lang.current');

    // if the current language is not set yet, get the code from the uri
    if(empty($currentCode)) {

      // get the uri including the language code
      $uri = new uri(null, array('subfolder' => site::instance()->subfolder()));

      // get the code of the current language if available
      $currentCode = $uri->path()->first();

    }

    // get the default language 
    $default = $this->findDefault();

    // try to find the current language
    if(in_array($currentCode, $this->codes()) and $current = $this->find($currentCode)) {
      // current is already set
    } else {
      $current = $default;
    }

    // mark the current language as current
    $current->isCurrent = true;

    // store the current language code and the default code in the config
    c::set('lang.current', $current->code());
    c::set('lang.default', $default->code());

  }  

  /**
   * Return all available language codes
   * 
   * @return array
   */
  public function codes() {
    if(!is_null($this->codes)) return $this->codes;
    return $this->codes = $this->keys();
  }

  /**
   * Returns the currently active language
   * 
   * @return object Language
   */
  public function findCurrent() {
    if(!is_null($this->current)) return $this->current;    
    foreach($this->data as $lang) {
      if($lang->isCurrent()) return $this->current = $lang;
    }
  }

  /**
   * Returns the default language
   * 
   * @return object Language
   */
  public function findDefault() {
    if(!is_null($this->default)) return $this->default;    
    foreach($this->data as $lang) {
      if($lang->isDefault()) return $this->default = $lang;
    }
  }

  /**
   * Returns the user's preferred language
   * 
   * @return object Language
   */
  public function findPreferred() {
    if(!is_null($this->preferred)) return $this->preferred;    
    foreach($this->data as $lang) {
      if($lang->isPreferred()) return $this->preferred = $lang;
    }
    return $this->preferred = $this->findDefault();
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