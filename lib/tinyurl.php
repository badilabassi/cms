<?php

namespace Kirby\CMS;

use Kirby\Toolkit\C;
use Kirby\Toolkit\URI;

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Tinyurl handler class
 *
 * @package   Kirby CMS
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://getkirby.com/license
 */
class Tinyurl {

  /**
   * Creates a tinyurl for the given page object
   * by using the page's hash and if necessary the current language code
   * 
   * @param object $page
   * @return string
   */
  static public function create(Page $page) {

    // if tiny urls are disabled, simply return the entire url  
    if(!c::get('tinyurl.enabled')) return $page->url();

    // build the base url 
    $url = site::instance()->url() . '/' . c::get('tinyurl.folder') . '/' . $page->hash();
      
    // if multi-language mode is activated and the current language is 
    // not the default language, add the language code
    if(site::$multilang and !site::instance()->language()->isDefault()) {
      $url .= '/' . site::instance()->language()->code();
    }

    return $url;

  }

  /**
   * Tries to resolve internal tinyurls by
   * parsing the given url and searching for a matching page
   * 
   * @param string $tinyurl
   * @return string
   */
  static public function resolve($tinyurl) {

    // check if tinyurls are enabled at all
    if(!c::get('tinyurl.enabled')) return false;

    // get the site object
    $site = site::instance();

    // parse the given tinyurl 
    $uri = new Uri($tinyurl, array(
      'subfolder' => site::instance()->subfolder()
    ));

    $folder = $uri->path()->nth(0);
    $hash   = $uri->path()->nth(1);
    $lang   = $uri->path()->nth(2);

    // check for a valid tinyurl
    if($folder != c::get('tinyurl.folder') or !$hash) return false;

    // multi-lang tiny url support
    if(site::$multilang) {
    
      // validate the language code
      $lang = ($site->languages()->find($lang)) ? $lang : null;

    }

    // search for the page by hash
    $page = $site->children()->findByHash($hash);

    // check if the page is usable/valid and redirect to it
    return ($page and !$page->isErrorPage()) ? $page->url($lang) : false;

  }

}