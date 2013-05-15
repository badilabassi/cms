<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Renders a vimeo tag 
 * ie. (vimeo: http://vimeo.com/52345557)
 * 
 * @package   Kirby CMS
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://getkirby.com/license
 */
class KirbytextVimeoTag extends KirbytextTag {

  // a list of allowed attributes for this tag
  protected $attr = array(
    'width',
    'height',
    'class'
  );

  /**
   * Returns the generated html for this tag
   * 
   * @return string
   */
  public function html() {

    return Embed::vimeo($this->value(), array(
      'width'  => $this->attr('width', c::get('kirbytext.video.width')), 
      'height' => $this->attr('height', c::get('kirbytext.video.height')), 
      'class'  => $this->attr('class')
    ));
  
  }

}