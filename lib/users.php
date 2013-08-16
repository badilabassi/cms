<?php 

namespace Kirby\CMS;

use Kirby\Toolkit\Collection;
use Kirby\Toolkit\Dir;
use Kirby\Toolkit\F;

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Users
 * 
 * A collection of all registered user accounts
 * 
 * @package   Kirby CMS
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://getkirby.com/license
 */
class Users extends Collection {

  /**
   * Custom constructor
   * 
   * you can either pass an array of user accounts
   * or null to fetch all accounts from the account directory
   *
   * @param mixed $data
   */
  public function __construct($data = null) {
  
    if(is_array($data)) {
      parent::__construct($data);
    } else {
    
      $files = dir::read(KIRBY_SITE_ROOT . DS . 'accounts');

      foreach($files as $file) {
        if(f::extension($file) != 'php') continue;
        $username = f::name($file);
        if($user = user::find($username)) {
          $this->set($username, $user);      
        }
      }

    }

  }

  /**
   * Returns all user accounts by a specifc group
   * 
   * @param string $group
   * @return object Returns a filtered Users collection
   */
  public function filterByGroup($group) {
    return $this->filterBy('group', $group);
  }

  /**
   * Creates a new user
   *
   * @see User::create
   * @param array $data 
   * @return mixed 
   */
  public function create($data) {
    return User::create($data);
  }

  /**
   * Makes it possible to echo the object
   * and get a readable list with all account names
   * 
   * @return string
   */
  public function __toString() {
    $html = array();
    foreach($this->toArray() as $user) {
      $html[] = $user->username();
    }
    return implode('<br />', $html);
  }

}