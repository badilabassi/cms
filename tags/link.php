<?php

namespace Kirby\CMS\Kirbytext\Tag;

use Kirby\CMS\Kirbytext\Tag;
use Kirby\Toolkit\HTML;

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Renders a link tag 
 * ie. (link: my/link text: my text) 
 * 
 * @package   Kirby CMS
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://getkirby.com/license
 */
class Link extends Tag {

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

    return html::a($url, html($this->attr('text')), array(
      'rel'    => $this->attr('rel'), 
      'class'  => $this->attr('class'), 
      'title'  => html($this->attr('title')),
      'target' => $this->target(),
    )); 

  }

}