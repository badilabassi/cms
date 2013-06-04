<?php

namespace Kirby\CMS\Kirbytext\Tag;

use Kirby\CMS\Kirbytext\Tag;
use Kirby\Toolkit\HTML;

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Renders a twitter tag 
 * ie. (twitter: getkirby)
 * 
 * @package   Kirby CMS
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://getkirby.com/license
 */
class Twitter extends Tag {
  
  // a list of allowed attributes for this tag
  protected $attr = array(
    'class',
    'title',
    'text',
    'rel',
    'target', 
    'popup',
  );

  /**
   * Returns the generated html for this tag
   * 
   * @return string
   */
  public function html() {
    
    // get and sanitize the username
    $username = str_replace('@', '', $this->value());
    
    // build the profile url
    $url = 'https://twitter.com/' . $username;

    // sanitize the link text
    $text = $this->attr('text', '@' . $username);

    // build the final link
    return html::a($url, $text, array(
      'class'  => $this->attr('class'), 
      'title'  => $this->attr('title'),
      'rel'    => $this->attr('rel'), 
      'target' => $this->target(),
    ));
  
  }

}