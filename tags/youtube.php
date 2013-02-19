<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Renders a youtube tag 
 * ie. (youtube: http://www.youtube.com/watch?v=_9tHtxOCvy4)
 */
class KirbyTextYoutubeTag extends KirbyTag {

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
  
    return Embed::youtube($this->value(), array(
      'width'  => $this->attr('width', c::get('kirbytext.video.width')), 
      'height' => $this->attr('height', c::get('kirbytext.video.height')), 
      'class'  => $this->attr('class')
    ));

  }

}