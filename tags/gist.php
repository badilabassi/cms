<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Renders a gist tag 
 * ie. (gist: https://gist.github.com/2924148)
 * 
 * @package   Kirby CMS
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://getkirby.com/license
 */
class KirbytextGistTag extends KirbytextTag {

  // a list of allowed attributes for this tag
  protected $attr = array(
    'file'
  );

  /**
   * Returns the generated html for this tag
   * 
   * @return string
   */
  public function html() {
    return Embed::gist($this->value(), $this->attr('file'));  
  }

}