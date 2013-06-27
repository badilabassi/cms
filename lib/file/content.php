<?php 

namespace Kirby\CMS\File;

use Kirby\Toolkit\A;
use Kirby\Toolkit\C;
use Kirby\Toolkit\F;
use Kirby\Toolkit\Str;
use Kirby\CMS\Site;
use Kirby\CMS\File;
use Kirby\CMS\Variable;

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Content
 * 
 * The content object is an extended File
 * object which is used for all content text files. 
 * 
 * Content objects are used for the main content, site info and 
 * meta information for other files. 
 * 
 * Page objects access their main content object
 * to return custom field data. 
 * 
 * i.e. $page->title() is the same as $page->content()->title()
 * 
 * Content objects have many child Variable objects
 * for each parsed field in the text file. 
 * So all custom field contents are Variable objects. 
 * 
 * @package   Kirby CMS
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://getkirby.com/license
 */
class Content extends File {

  // cache for the raw content of the file
  protected $raw = null;

  // the data array with all fields/Variables
  protected $data = null;

  // cache for the detected language code
  protected $languageCode = null;

  /**
   * Constructor
   * 
   * @param object $file The parent File object
   */
  public function __construct(File $file) {

    $this->root      = $file->root();
    $this->parent    = $file->parent();
    $this->type      = 'content';
    $this->filename  = $file->filename();
    $this->extension = $file->extension();

  }

  /**
   * Returns the plain text version of the text file
   * This also makes sure to remove BOM characters from 
   * text files to avoid parsing errors. 
   * 
   * @return string
   */
  public function raw() {

    if(!is_null($this->raw)) return $this->raw;

    $this->raw = f::read($this->root);
    $this->raw = str_replace("\xEF\xBB\xBF", '', $this->raw);    

    return $this->raw;

  }

  /**
   * Returns the name of the file without extension   
   * and without language code
   *
   * @return string
   */
  public function name() {
  
    if(!is_null($this->name)) return $this->name;

    $name = f::name($this->filename());
    $name = preg_replace('!.(' . implode('|',c::get('lang.available')) . ')$!i', '', $name);

    return $this->name = $name;
  
  }

  /**
   * Returns an array with all field names from the text file
   * 
   * @return array 
   */
  public function fields() {
  
    // if language support is switched off or this is the default language
    // file, simply return an array of array keys of the data array    
    if(!site::$multilang or $this->isDefaultContent()) return array_keys($this->data());

    // when language support is switched on, always look for 
    // the right fields in the default language content file
    return $this->page()->content(c::get('lang.default'))->fields();

  }

  /**
   * Returns an array with all keys and values/Variables
   * 
   * @param string $key Optional key to get a single item from the data array
   * @param mixed $default Optional default value if the item is not in the array
   * @return array
   */
  public function data($key = null, $default = null) {

    // getter for a specific data key
    if(!is_null($key)) {

      // if language support is switched off, or this is the default
      // language file, a fallback for missing/untranslated fields is not needed
      if(!site::$multilang or $this->isDefaultContent()) {
        return a::get($this->data(), $key, $default);
      }

      // get the full data array
      $data = $this->data();

      // if the field exists, just return its content 
      if(isset($data[$key])) return $data[$key];

      // load the default language content file for the parent page
      // and try to get the field from that file as a fallback
      return $this->page()->defaultContent()->$key();

    }

    // getter for the entire data array
    if(!is_null($this->data)) return $this->data;

    $raw = $this->raw();

    if(!$raw) return $this->data = array();

    $sections = preg_split('![\r\n]+[-]{4,}!i', $raw);
    $data     = array();
    
    foreach($sections AS $s) {

      $parts = explode(':', $s);  
      $key   = str::lower(preg_replace('![^a-z0-9]+!i', '_', trim($parts[0])));

      if(empty($key)) continue;
      
      $value = trim(implode(':', array_slice($parts, 1)));

      // store the key and value in the data array
      $this->data[$key] = new Variable($key, $value, $this);
    
    }

    return $this->data;

  }

  /**
   * Returns the language code of this file
   * If the file has no language code, 
   * the default language code will be returned
   * 
   * @return string
   */
  public function languageCode() {
    if(!is_null($this->languageCode)) return $this->languageCode;    
    $code = str::match($this->filename(), '!\.([a-z]{2})\.' . $this->extension() . '$!i', 1);
    return $this->languageCode = (empty($code) || !in_array($code, c::get('lang.available'))) ? c::get('lang.default') : $code;
  }

  /**
   * Checks if this is the content file for 
   * the default language. This is used to check for needed
   * fallbacks for missing stuff in translated files
   * 
   * @return boolean
   */
  public function isDefaultContent() {
    return c::get('lang.default') == $this->languageCode() ? true : false;
  }

  /**
   * Magic getter for Variables
   * i.e. $this->title()
   * 
   * @param string $key This is auto filled by PHP with the called method name
   * @param mixed $arguments Not used!
   * @return mixed
   */
  public function __call($key, $arguments = null) {    
    return $this->data($key);
  }

  /**
   * Legacy code to implement content->variables;
   */
  public function __get($key) {
    if($key == 'variables') {
      return $this->data();
    } else {
      return $this->$key();
    }
  }

}