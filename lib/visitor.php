<?php

namespace Kirby\CMS;

use Kirby\Toolkit\C;

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Visitor
 * 
 * @package   Kirby CMS
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://getkirby.com/license
 */
class Visitor extends \Kirby\Toolkit\Visitor {

  // cache for the detected language
  static protected $language = null;

  /**
   * Returns the user's accepted language code
   * 
   * @return string
   */
  static public function acceptedLanguageCode() {
    if(!is_null(static::$acceptedLanguageCode)) return static::$acceptedLanguageCode;
    $detected = parent::acceptedLanguageCode();
    $detected = language::valid($detected) ? $detected : c::get('lang.default');    
    return static::$acceptedLanguageCode = $detected;
  }

  /**
   * Returns the preferred language of the visitor
   * If no preferred language is found, the 
   * default language will be returned
   * 
   * @return object KirbyLanguage
   */
  static public function language() {
  
    if(!site::$multilang) return false;

    if(!is_null(static::$language)) return static::$language;   
 
    // try to find the language in the available languages collection
    $language = site::instance()->languages()->find(static::acceptedLanguageCode());

    // otherwise replace it with the default language
    if(!$language) $language = site()->languages()->findDefault();

    // store and return
    return static::$language = $language;
  
  }
  
}