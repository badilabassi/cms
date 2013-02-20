<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Renders a dynamic date
 * ie. (date: year)
 * 
 * You can use the year keyword to get the current year
 * or php date format strings to get whatever date you need
 */
class KirbyTextDateTag extends KirbyTag {

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