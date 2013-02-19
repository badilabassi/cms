<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Renders a gist tag 
 * ie. (gist: https://gist.github.com/2924148)
 */
class KirbyTextGistTag extends KirbyTag {

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