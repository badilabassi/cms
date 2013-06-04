<?php

namespace Kirby\CMS\Kirbytext\Tag;

use Kirby\CMS\Kirbytext\Tag;
use Kirby\Toolkit\Str;

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Renders a dynamic date
 * ie. (date: year)
 * 
 * You can use the year keyword to get the current year
 * or php date format strings to get whatever date you need
 * 
 * @package   Kirby CMS
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://getkirby.com/license
 */
class Date extends Tag {

  // a list of allowed attributes for this tag
  protected $attr = array();

  /**
   * Returns the generated html for this tag
   * 
   * @return string
   */
  public function html() {
    return (str::lower($this->value()) == 'year') ? date('Y') : date($this->value());
  }

}