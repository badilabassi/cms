<?php

namespace Kirby\CMS\Kirbytext\Tag;

use Kirby\CMS\Kirbytext\Tag;
use Kirby\Toolkit\C;
use Kirby\Toolkit\Embed;

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Renders a youtube tag 
 * ie. (youtube: http://www.youtube.com/watch?v=_9tHtxOCvy4)
 * 
 * @package   Kirby CMS
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://getkirby.com/license
 */
class Youtube extends Tag {

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
  
    return embed::youtube($this->value(), array(
      'width'  => $this->attr('width', c::get('kirbytext.video.width')), 
      'height' => $this->attr('height', c::get('kirbytext.video.height')), 
      'class'  => $this->attr('class')
    ));

  }

}