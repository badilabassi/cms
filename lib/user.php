<?php 

namespace Kirby\CMS;

use Kirby\Toolkit\A;
use Kirby\Toolkit\Crypt;
use Kirby\Toolkit\Cookie;
use Kirby\Toolkit\F;
use Kirby\Toolkit\Model;
use Kirby\Toolkit\Password;
use Kirby\Toolkit\S;
use Kirby\Toolkit\Str;

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * User
 * 
 * A central user model, which is used for all panel
 * and all site users. Accounts are stored in encrypted
 * files in site/accounts. Avatars for users are stored by 
 * default in assets/avatars
 * 
 * @package   Kirby CMS
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://getkirby.com/license
 */
class User extends Model {

  // The status is used within the save() method to 
  // get the exists validation right
  public $isNew;
  
  // Cache for the avatar object 
  protected $avatar;

  /**
   * Returns the full root to the encrypted account file
   * 
   * @return string
   */
  public function file() {
    return KIRBY_SITE_ROOT . DS . 'accounts' . DS . $this->username() . '.php';
  }

  /**
   * Custom setter for passwords, which hashes the password
   * before it gets stored
   * 
   * @param string $value The plain password
   */
  public function setPassword($value) {

    if(empty($value)) {
      $this->write('password', null);
      return false;
    } else if($value == $this->username()) {
      $this->raise('The password must not match the username', 'password');
      $this->write('password', null);
      return false;
    }

    $this->write('password', password::hash($value));
  
  }

  /**
   * Custom setter for the group, which checks 
   * for a sufficient number of root accounts 
   * 
   * @param string $value The name of the group
   */
  public function setGroup($value) {

    // check if it is ok to make this the root user
    if($value == 'root') {

      // there's already a root user and there can only be one
      if(site::instance()->users()->filterByGroup('root')->not($this->username())->count() > 0) {
        return false;
      }

    // check if we still need a root user
    } else {
  
      // if no other root user is left, make sure this one will be a root user  
      if(site::instance()->users()->filterByGroup('root')->not($this->username())->count() == 0) {
        $value = 'root';
      }

    }

    $this->write('group', $value);

  }

  /**
   * User validation
   */
  public function validate() {

    // check for an writable account file
    if(!$this->isEditable()) $this->raise('The account file is not writable', 'file');

    // validate th user data
    v($this, array(
      'username' => array('required', 'min' => 3),
      'email'    => array('required', 'email'),
      //'group'  => array('in' => Group::all()->keys()), 
      'password' => array('required'),
    ));

    if($this->isNew or ($this->old('username') and $this->old('username') != $this->username())) {
      if(User::find($this->username())) $this->raise('The username is already taken', 'username');     
    }

  }

  /**
   * Saves all the data for this user in the account file
   * and encrypts it properly.  
   * 
   * @return boolean
   */
  public function save() {

    // validation
    $this->validate();
                       
    // stop saving process when errors occurred
    if($this->invalid()) return false;

    // encrypt the content
    $content = '<?php //' . crypt::encode(serialize($this->data), $this->username());    
    
    // try to write the user file
    if(!f::write($this->file(), $content)) {
      $this->raise('The user file could not be saved', 'write-error');
      return false;
    }

    return true;

  }

  /**
   * Deletes the account if this is not the root user
   * 
   * @return boolean
   */
  public function delete() {    
    // the root user cannot be deleted
    if($this->isRoot()) return false;

    // delete the avatar as well
    $this->avatar()->delete();

    // delete the account file
    return f::remove($this->file());
  }

  /**
   * Logs this user in by password
   * 
   * @param string $password The plaintext password coming from the login form
   * @return boolean
   */
  public function login($password) {

    // make sure the currently logged in user is logged out
    cookie::remove('kirby-auth');        
    
    s::remove('kirby-auth');
    s::remove('kirby-user');

    // check if the user's password matches the passed password
    if(!password::match($password, $this->password())) return false;

    // generate a new random access token
    $token = str::random(32);
    
    // store the token in the cookie
    // and the user data in the session    
    cookie::set('kirby-auth', $token, 60*24);        
    
    // store all the rest in the session
    s::set('kirby-auth', $token);
    s::set('kirby-user', $this->username());

    // store the token in the user file as well
    $this->token = $token;
    $this->save();

    return true;

  }

  /**
   * Logs out the current user
   */
  public function logout() {

    if($this->isCurrent()) {
      cookie::remove('kirby-auth');
      s::remove('kirby-auth');
      s::remove('kirby-user');
    }

    // overwrite this with a new token
    $this->token = str::random(32);
    $this->save();

  }

  /**
   * Checks if this is a new user or an 
   * existing user
   * 
   * @return boolean
   */
  public function isNew() {
    return $this->isNew;
  }

  /**
   * Checks if this is the root user
   * 
   * @return boolean
   */
  public function isRoot() {
    return $this->group() == 'root';
  }

  /**
   * Checks if this is the admin user
   * 
   * @return boolean
   */
  public function isAdmin() {
    return $this->group() == 'admin' or $this->isRoot();
  }

  /**
   * Checks if the user account has sufficient permissions
   * to be updated/created via PHP
   * 
   * @return boolean
   */
  public function isEditable() {

    // check for the account file
    $file = $this->file();

    // creatable?
    if(!file_exists($file)) {
      $dir = dirname($file);
      return is_dir($dir) and is_writable($dir);
    } else {
      return is_file($file) and is_writable($file);      
    }

  }

  /**
   * Checks if this user has access to the panel
   * 
   * @return boolean
   */
  public function hasPanelAccess() {
    return $this->isAdmin();
  }

  /**
   * Checks if this is the currently logged in user
   * 
   * @return boolean
   */
  public function isCurrent() {
    return $this->is(User::current());
  }

  /**
   * Checks if this user is logged in
   * 
   * @return boolean
   */
  public function isLoggedIn() {
    return $this->isCurrent();
  }

  /**
   * Returns the avatar object for this user
   * 
   * @param mixed $params An optional array or string of params for the avatar class
   * @return object
   */
  public function avatar($params = null) {
    if(!is_null($params)) {
      return $this->avatar()->thumb($params);
    }
    if(!is_null($this->avatar)) return $this->avatar;
    return $this->avatar = new Avatar($this);
  }

  /**
   * Finds a user by username and returns the user model
   * 
   * @param string $username
   * @return mixed Returns the model or false
   */
  static public function find($username) {

    $file = KIRBY_SITE_ROOT . DS . 'accounts' . DS . $username . '.php';

    // stop when the account file is missing
    if(!file_exists($file)) return false;

    $data = f::read($file);
    $data = ltrim($data, '<?php //');
    $data = crypt::decode($data, $username);
    $data = @unserialize($data);

    if(!is_array($data) or empty($data)) return false; 

    // get the password from the user file
    $password = a::get($data, 'password');

    // get the raw group
    $group = a::get($data, 'group');

    // a user without password does not exist
    if(empty($password)) return false;

    // remove the password from the list so it won't get hashed again
    unset($data['password']);
    unset($data['group']);

    // create a new user object
    $user = new User($data);

    // add the password without hashing
    $user->write('password', $password);
    
    // add the group without using the custom setter
    $user->write('group', $group);

    // mark this user as "old"
    $user->isNew = false;

    // make sure the user is valid
    $user->validate();

    // don't return invalid user objects
    return $user->valid() ? $user : false;

  }

  /**
   * Creates a new user
   * 
   * @param array $data An array with data for the new user model
   * @return mixed False if the creation failed or the model
   */
  static public function create($data = array()) {

    $user = new User($data);
    $user->isNew = true;

    // if there's no other root user so far, we need one
    if(site::instance()->users()->filterByGroup('root')->count() == 0) {
      $user->group = 'root';
    }

    $user->save();

    // make sure the new user is availabel
    site::instance()->reset();
    return $user;
  }

  /**
   * Returns the currently logged in user
   * 
   * @return mixed False if no user can be found, otherwise returns the model
   */
  static public function current() {

    $token    = cookie::get('kirby-auth');
    $username = s::get('kirby-user');

    if(empty($token) or empty($username) or $token != s::get('kirby-auth')) return false;
    
    if($user = user::find($username)) {
      if($token == $user->token()) return $user;
    }

    return false;
  
  }

}