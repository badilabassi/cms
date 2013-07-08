<?php 

namespace Kirby\CMS;

use Kirby\Toolkit\A;
use Kirby\Toolkit\C;

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Language
 * 
 * Represents a single available language for multi-language sites
 * 
 * @package   Kirby CMS
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://getkirby.com/license
 */
class Language {

  protected $code   = null;
  protected $name   = null;
  protected $locale = null;

  protected $isDefault;
  protected $isAvailable;

  public $isCurrent;

  /**
   * Constructor
   * 
   * @param string $code The 2-char language code
   */
  public function __construct($params = array()) {

    $defaults = array(
      'code'      => 'en',
      'name'      => 'English', 
      'locale'    => null,
      'default'   => false,
      'current'   => false, 
      'available' => true, 
    );

    $options = array_merge($defaults, $params);

    $this->code        = $options['code'];
    $this->name        = $options['name'];
    $this->locale      = $options['locale'];
    $this->isDefault   = $options['default'];
    $this->isAvailable = $options['available'];
    $this->isCurrent   = $options['current'];
  
  }

  /**
   * Returns the name of the language
   * 
   * @return string 
   */
  public function name() {
    return $this->name;
  }

  /**
   * Returns the locale of the language if available
   * 
   * @return string 
   */
  public function locale() {
    return $this->locale;
  }

  /**
   * Returns the 2-char language code 
   * 
   * @return string
   */
  public function code() {
    return $this->code;
  }

  /**
   * Returns the main URL for this language
   * i.e. http://yourdomain.com/en
   * 
   * @return string
   */
  public function url() {    
    return $this->isDefault() && c::get('lang.urls') == 'short' ? site::instance()->url() : site::instance()->url() . '/' . $this->code();
  }

  /**
   * Checks if this is the preferred 
   * language of the current user. 
   * The user agent string will be 
   * checked to guess the preferred language
   * 
   * @see Visitor
   * @return boolean
   */
  public function isPreferred() {
    return site::instance()->visitor()->language() == $this ? true : false;
  }

  /**
   * Checks if this language is available
   * or currently deactivated
   * 
   * @return boolean
   */
  public function isAvailable() {
    return $this->isAvailable;
  }

  /**
   * Checks if this language is
   * currently active
   * 
   * @return boolean
   */
  public function isCurrent() {
    return $this->isCurrent;
  }

  /**
   * Checks if this is the default language
   * 
   * @return boolean
   */
  public function isDefault() {
    return $this->isDefault;
  }

  /**
   * Checks if the passed code is valid
   * 
   * @return boolean
   */
  static public function valid($code) {
    return (in_array($code, site::instance()->languages()->codes())) ? true : false;
  }

  /**
   * Returns the language code if the object 
   * is being converted to a string or echoed
   *
   * @return string
   */
  public function __toString() {
    return $this->code;
  }

  /**
   * Returns a more readable dump array for the dump() helper
   * 
   * @return array
   */
  public function __toDump() {

    return array(
      'code'      => $this->code(),
      'name'      => $this->name(),
      'url'       => $this->url(),
      'available' => $this->isAvailable(), 
      'current'   => $this->isCurrent(),
      'default'   => $this->isDefault(),
      'preferred' => $this->isPreferred(), 
      'locale'    => $this->locale(),
    );

  }

}