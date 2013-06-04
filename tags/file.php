<?php

namespace Kirby\CMS\Kirbytext\Tag;

use Kirby\CMS\Kirbytext\Tag;
use Kirby\Toolkit\HTML;
use Kirby\Toolkit\F;

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Renders an file tag 
 * ie. (file: myfile.pdf)
 * 
 * @package   Kirby CMS
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://getkirby.com/license
 */
class File extends Tag {

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

    return html::a($url, html($text), array(
      'class'  => $this->attr('class'), 
      'title'  => html($this->attr('title')),
      'rel'    => $this->attr('rel'), 
      'target' => $this->target(),
    ));
  
  }

}