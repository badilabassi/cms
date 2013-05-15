<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Renders a flash tag 
 * ie. (flash: http://superurl.com/myflash.flv)
 * 
 * @package   Kirby CMS
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://getkirby.com/license
 */
class KirbytextFlashTag extends KirbytextTag {

  // a list of allowed attributes for this tag
  protected $attr = array(
    'width',
    'height',
  );

  /**
   * Returns the generated html for this tag
   * 
   * @return string
   */
  public function html() {

    $width  = $this->attr('width', c::get('kirbytext.video.width'));
    $height = $this->attr('height', c::get('kirbytext.video.height'));

    return '<div class="video">' . Embed::flash($this->value(), $width, $height) . '</div>';
  
  }

}