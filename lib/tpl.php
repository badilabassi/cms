<?php

namespace Kirby\CMS;

use Kirby\Toolkit\A;
use Kirby\Toolkit\Template;

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Deprecated way of adding variables to templates and snippets
 * 
 * @package   Kirby CMS
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://getkirby.com/license
 */
class TPL {

  /**
   * Global Setter
   */
  static public function set() {
    call_user_func_array(array('template','globals'), func_get_args());
  }

  /**
   * Global Getter
   */
  static public function get($key = null, $default = null) {
    if(is_null($key)) return template::globals();
    return a::get(template::globals(), $key, $default);
  }   

}