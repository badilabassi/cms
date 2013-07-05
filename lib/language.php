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

  protected $code = null;

  /**
   * Constructor
   * 
   * @param string $code The 2-char language code
   */
  public function __construct($code, $isActive = false) {
    $this->code     = $code;
    $this->isActive = $isActive;
  }

  /**
   * Returns the name of the language if available
   * To enable this feature, language names must be 
   * set in your config in an associative array 
   * 
   * i.e. c::set('lang.names', array('en' => 'English'));
   * 
   * @return string 
   */
  public function name() {
    $names = c::get('lang.names', array());
    return a::get($names, $this->code);
  }

  /**
   * Returns the locale of the language if available
   * To enable this feature, locales must be 
   * set in your config in an associative array 
   * 
   * i.e. c::set('lang.locales', array('en' => 'en_US'));
   * 
   * @return string 
   */
  public function locale() {
    $locales = c::get('lang.locales', array());
    return a::get($locales, $this->code);
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
    return $this->isDefault() && c::get('lang.default.longurl') == false ? site::instance()->url() : site::instance()->url() . '/' . $this->code();
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
    return (in_array($this->code, c::get('lang.available'))) ? true : false;
  }

  /**
   * Checks if this language is
   * currently active
   * 
   * @return boolean
   */
  public function isActive() {
    return $this->isActive;
  }

  /**
   * Checks if this is the default language
   * 
   * @return boolean
   */
  public function isDefault() {
    return (c::get('lang.default') == $this->code) ? true : false;
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
      'active'    => $this->isActive(),
      'default'   => $this->isDefault(),
      'preferred' => $this->isPreferred(), 
      'locale'    => $this->locale(),
    );

  }

}