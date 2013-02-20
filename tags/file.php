<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Renders an file tag 
 * ie. (file: myfile.pdf)
 */
class KirbyTextFileTag extends KirbyTag {

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

    // build a proper link to the file
    $url  = $this->url($this->value());
    $text = $this->attr('text');

    // use filename if the text is empty and make sure to 
    // ignore markdown italic underscores in filenames
    if(empty($text)) $text = str_replace('_', '\_', f::filename($url)); 

    return Html::a($url, html($text), array(
      'class'  => $this->attr('class'), 
      'title'  => html($this->attr('title')),
      'rel'    => $this->attr('rel'), 
      'target' => $this->target(),
    ));
  
  }

}