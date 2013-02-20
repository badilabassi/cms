<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Renders an email tag 
 * ie. (email: bastian@getkirby.com)
 */
class KirbyTextEmailTag extends KirbyTag {

  // a list of allowed attributes for this tag
  protected $attr = array(
    'class', 
    'title', 
    'rel'
  );

  /**
   * Returns the generated html for this tag
   * 
   * @return string
   */
  public function html() {
 
    return Html::email($this->value(), html($this->attr('text')), array(
      'class' => $this->attr('class'),
      'title' => $this->attr('title'),      
      'rel'   => $this->attr('rel'),      
    ));
 
  }

}