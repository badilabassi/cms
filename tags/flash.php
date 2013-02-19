<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Renders a flash tag 
 * ie. (flash: http://superurl.com/myflash.flv)
 */
class KirbyTextFlashTag extends KirbyTag {

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