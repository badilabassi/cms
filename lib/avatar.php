<?php 

namespace Kirby\CMS;

use Kirby\Toolkit\A;
use Kirby\Toolkit\C;
use Kirby\Toolkit\Asset;
use Kirby\Toolkit\F;
use Kirby\Toolkit\Thumb;

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Avatar
 * 
 * Handles user avatars for the panel and the site
 * 
 * @package   Kirby CMS
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://getkirby.com/license
 */
class Avatar {

  // the full root of the avatar
  protected $root;
  
  // the public url of the avatar
  protected $url;
  
  // the paren asset object
  protected $asset;

  /**
   * Constructor
   * 
   * @param object $user
   */
  public function __construct(User $user) {    

    $root = c::get('user.avatar.root');
    $url  = c::get('user.avatar.url');
    $file = glob($root . DS . $user->username() . '.{jpg,jpeg,gif,png}', GLOB_BRACE);

    if(empty($root)) $root = KIRBY_INDEX_ROOT . DS . 'assets' . DS . 'avatars';
    if(empty($url))  $url  = site::instance()->baseurl() . '/assets/avatars';

    if(empty($file)) {
      $this->root = $root . DS . $user->username() . '.jpg';
      $this->url  = $url . '/' . $user->username() . '.jpg';
    } else {
      $file = a::first($file);
      $this->root = $file;
      $this->url  = $url . '/' . f::filename($file);
    }

    $this->asset = new Asset($this->root, $this->url);
  
  }

  /**
   * Shortcut to generate a thumbnail for an avatar
   * Only applicable for appropriate image formats
   * 
   * @return mixed Thumb object for images, false for others.
   */
  public function thumb($params) {

    // only generate thumbnails for appropriate image formats
    if(!in_array($this->mime(), array('image/gif', 'image/png', 'image/jpeg'))) return false;

    // check if the file exists at all
    if(!$this->exists()) return false;

    // return the Thumbnail object
    return new Thumb($this, $params);
  
  }

  /**
   * Magic caller, which is used to 
   * access all available methods from the parent asset object
   * 
   * @param string $method
   * @param mixed $arguments
   * @return mixed
   */
  public function __call($method, $arguments) {
    if(method_exists($this->asset, $method)) {
      return call_user_func_array(array($this->asset, $method), $arguments);
    } else {
      raise('Invalid avatar method: ' . $method);
    }
  }  

}