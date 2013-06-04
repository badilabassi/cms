<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Kirbytext Tag
 * 
 * The mother class for all Kirbytext tags
 * Extend this to build a new KirbytextTag. 
 * Kirbytags are located in kirby/tags or site/kirbytags
 * 
 * @package   Kirby CMS
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://getkirby.com/license
 */
class KirbytextTag {

  // the parent kirbytext object
  protected $kirbytext = null;
  
  // the name of the current tag
  protected $name = null;
  
  // a list of allowed attributes for this tag
  protected $attr = array();

  // a list of parsed attributes
  protected $data = array();
  
  // the main value of this tag 
  // ie. (image: myimage.jpg) value = myimage.jpg
  protected $value = null;

  /**
   * Constructor
   * 
   * @param array $params A list of parameters for this tag. You can either pass array('name' => 'mytag', 'kirbytext' => $kirbytextobject, 'tag' => '(mytag: value attr: myattr') or array('name' => 'mytag', 'kirbytext' => $kirbytextobject, 'value' => 'myvalue', 'attr' => array('attr1' => 'myattr'))
   */
  public function __construct($params = array()) {

    // apply the name of this tag
    $this->name = $params['name'];
    
    // store the parent kirbytext object
    $this->kirbytext = $params['kirbytext'];

    // if a full tag is given, parse all attributes
    if(isset($params['tag'])) {

      $replace = array('(', ')');            
      $tag     = str_replace($replace, '', $params['tag']);
      $attr    = array_merge(array($this->name), $this->attr);
      $search  = preg_split('!(' . implode('|', $attr) . '):!i', $tag, false, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);
      $result  = array();
      $num     = 0;
      
      foreach($search AS $key) {
      
        if(!isset($search[$num+1])) break;
        
        $key   = trim($search[$num]);
        $value = trim($search[$num+1]);

        $result[$key] = $value;
        $num = $num+2;

      }

      $this->data  = $result;
      $this->value = $this->data[$this->name];

      unset($this->data[$this->name]);

    // otherwise simply assign the given params
    } else {
      $this->data  = $params['attr'];
      $this->value = $params['value'];
    }

  }

  /**
   * Returns the parent kirbytext object
   * 
   * @return object Kirbytext
   */
  protected function kirbytext() {
    return $this->kirbytext;
  }

  /**
   * Returns the parent active page
   * 
   * @return object Page
   */
  protected function page() {
    return $this->kirbytext->page();
  }

  /**
   * Smart url generator
   * This tries to find matching files for the given uri first. 
   * Otherwise it will try to build an absolute url if not yet absolute.
   * 
   * @param string $url any uri for internal links, absolute urls or files
   * @return string 
   */
  protected function url($url) {

    // check if there's a file for this url
    if($file = $this->file($url)) return $file->url();

    // make sure to build a proper absolute url
    return url($url, $this->lang());

  }

  /**
   * Tries to find all related files for the current page
   * 
   * @return object Files
   */
  protected function files() {
    return ($this->page()) ? $this->page()->files() : null;
  }

  /**
   * Tries to find a file for the given url/uri
   * 
   * @param string $url a full path to a file or just a filename for files form the current active page
   * @return object File
   */
  protected function file($url) {
    
    // if this is an absolute url cancel
    if(preg_match('!(http|https)\:\/\/!i', $url)) return false;
    
    // skip urls without extensions
    if(!preg_match('!\.[a-z]+$!',$url)) return false;

    // try to get all files for the current page
    $files = $this->files();
    
    // cancel if no files are available
    if(!$files) return false;

    // try to find the file
    return $files->find($url);
            
  }

  /**
   * Returns the main value for this tag
   * ie. (image: myimage.jpg) value = myimage.jpg
   * 
   * @return string
   */
  protected function value() {
    return $this->value;
  }

  /**
   * Returns either a single attribute or all attributes
   * 
   * @param string $key An optional key of the attribute. If null, the entire list of attributes will be returned
   * @param mixed $default An optional default value if the attribute is empty or not existing.
   * @return string The value of the attribute
   */
  protected function attr($key = null, $default = null) {
    return a::get($this->data, $key, $default);
  }

  /**
   * Smart getter for the applicable target attribute. 
   * This will watch for popup or target attributes and return 
   * a proper target value if available. 
   * 
   * @return string 
   */
  protected function target() {
    if(empty($this->data['popup']) && empty($this->data['target'])) return false;
    return (empty($this->data['popup'])) ? $this->data['target'] : '_blank';
  }

  /**
   * Smart getter for the applicable language attribute
   * This will watch for a lang attribute and if multi-lang support is available
   * it will return the value of that attribute
   * 
   * @return string
   */
  protected function lang() {
    // language attribute is only allowed when lang support is activated
    return (!empty($this->data['lang']) && c::get('lang.support')) ? $this->data['lang'] : false;
  }

  /**
   * This is an empty placeholder for child classes. 
   * Use this in all tag classes to generate the html for the tag. 
   */
  public function html() {
    return false;
  }

}