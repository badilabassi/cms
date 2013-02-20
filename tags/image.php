<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Renders an image tag 
 * ie. (image: myimage.jpg)
 */
class KirbyTextImageTag extends KirbyTag {

  // a list of allowed attributes for this tag
  protected $attr = array(
    'width',
    'height',
    'alt',
    'text',
    'title',
    'class',
    'link',
    'target',
    'rel'
  );

  /**
   * Returns the generated html for this tag
   * 
   * @return string
   */
  public function html() {

    $url   = $this->value();
    $alt   = $this->attr('alt');
    $title = $this->attr('title');
    $link  = $this->attr('link');
    $file  = $this->file($url); 

    // use the file url if available and otherwise the given url
    $url = $file ? $file->url() : url($url);

    // alt is just an alternative for text
    if($text = $this->attr('text')) $alt = $text;
    
    // try to get the title from the image object and use it as alt text
    if($file) {
      
      if(empty($alt) && $file->alt() != '') {
        $alt = $file->alt();
      }

      if(empty($title) && $file->title() != '') {
        $title = $file->title();
      }

    }
            
    $image = Html::img($url, array(
      'width'  => $this->attr('width'), 
      'height' => $this->attr('height'), 
      'class'  => $this->attr('class'), 
      'title'  => html($title), 
      'alt'    => html($alt)
    ));

    if(!$this->attr('link')) return $image;

    // build the href for the link
    $href = ($link == 'self') ? $url : $link;
    
    return Html::a($href, $image, array(
      'rel'    => $this->attr('rel'), 
      'class'  => $this->attr('class'), 
      'title'  => html($this->attr('title')), 
      'target' => $this->target()
    ));

  }

}
