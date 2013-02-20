<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Renders a link tag 
 * ie. (link: my/link text: my text) 
 */
class KirbyTextLinkTag extends KirbyTag {

  // a list of allowed attributes for this tag
  protected $attr = array(
    'text', 
    'class', 
    'title', 
    'rel', 
    'target', 
    'popup'
  );

  /**
   * Returns the generated html for this tag
   * 
   * @return string
   */
  public function html() {
  
    // create a proper url
    $url = $this->url($this->value());

    return Html::a($url, html($this->attr('text')), array(
      'rel'    => $this->attr('rel'), 
      'class'  => $this->attr('class'), 
      'title'  => html($this->attr('title')),
      'target' => $this->target(),
    )); 

  }

}