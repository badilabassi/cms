<?php 

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Pages
 * 
 * This is the main collection object 
 * for a set of KirbyPage objects
 * 
 * @package Kirby CMS
 */
class KirbyPages extends KirbyCollection {

  // cache for all visible pages in this collection
  protected $visible = null;

  // cache for all invisible pages in this collection
  protected $invisible = null;

  // cache for all children of pages in this collection
  protected $children = null;

  // cache for a full indexed array of this collection
  protected $index = null;

  /**
   * Constructor
   * 
   * @param mixed $input Can either be a KirbyPage object (All children will be auto-added) or an array of KirbyPage objects
   */
  public function __construct($input = array()) {

    if(is_a($input, 'KirbyPage')) {

      $uri = is_a($input, 'KirbySite')  ? '' : $input->uri();

      foreach($input->dir()->children() as $dir) {
        
        $child = new KirbyPage($dir);
        $child->uri($uri . '/' . $child->uid());

        $this->set($child->uri(), $child);
      
      }

    } else if(is_array($input)) {

      foreach($input as $page) {  
        
        if(!is_a($page, 'KirbyPage')) raise('All pages in a set of KirbyPages have to be KirbyPage objects');
        
        // add the page to the collection
        $this->_['_' . $page->uri()] = $page;
      
      }

    } else {
      raise('KirbyPages must be constructed with a KirbyPage object or an array of KirbyPages');
    }

  }

  /**
   * Creates a clean one-level array with all 
   * pages, subpages, subsubpages, etc.
   *
   * @param object KirbyPages object for recursive indexing
   * @return array
   */
  public function index(KirbyPages $obj=null) {
    
    if(is_null($obj)) {
      if(!is_null($this->index)) return $this->index;
      $obj = $this;
    }

    foreach($obj->toArray() as $key => $page) {
      $this->index[$page->uri()] = $page;
      $this->index($page->children());
    }
    
    return $this->index;
            
  }

  /**
   * Merges all children of all pages
   *
   * @return object KirbyPages
   */
  public function children() {

    if(!is_null($this->children)) return $this->children;

    $children = array();

    foreach($this->toArray() as $page) {
      foreach($page->children() as $child) {
        $children[$child->uri()] = $child;  
      }      
    }

    return $this->children = new KirbyPages($children);

  }

  /**
   * Counts all children of all pages
   *
   * @return int
   */
  public function countChildren() {
    return $this->children()->count();
  }

  /**
   * Returns the active page in the collection
   *
   * @return object KirbyPage
   */
  public function active() {
    return site()->activePage();
  }

  /**
   * Finds pages by their URI. 
   * 
   * i.e. $pages->find('this/is/the/uri/to/my/page');
   * 
   * In this case a single page will be returned
   * 
   * It can also find multiple pages, by passing multiple
   * URIs as arguments: 
   * 
   * i.e. $pages->find('page-1', 'page-1/subpage-1', 'page-2');
   * 
   * In this case a KirbyPages object will be returned
   *
   * @param list Either a single URI as first argument or multiple URIs as a list of arguments. 
   * @return mixed Either a KirbyPage object, a KirbyPages object for multiple pages or null if nothing could be found
   */
  public function find() {
    
    $args = func_get_args();
  
    // find multiple pages
    if(count($args) > 1) {
      $result = array();
      foreach($args as $arg) {
        if($page = $this->find($arg)) $result[$page->uri()] = $page;
      }      
      return (empty($result)) ? false : new KirbyPages($result);
    }    
    
    // find a single page
    $path  = a::first($args);      
    $array = str::split($path, '/');
    $obj   = $this;
    $page  = false;
    $lang  = c::get('lang.support');

    foreach($array as $p) {    

      $by   = ($lang) ? 'translatedUID' : 'uid';
      $next = $obj->findBy($by, $p, false);

      if(!$next) return $page;

      $page = $next;
      $obj  = $next->children();
    }
    
    return $page;    
  
  }

  /**
   * Finds a single element in a set of pages by a given key and value
   * 
   * @param string $key The name of the key/field to search for
   * @param mixed $value The value to match against. Array: the method will search for multiple values and return a KirbyPages collection of results. 
   * @param boolean $deep true: the method will search all children, grantchildren, etc., false: the method will only search the current set
   * @return mixed KirbyPage if a single page is found, KirbyPages if multiple values have been passed and multiple pages are found or null if nothing could be found
   */
  public function findBy($key, $value, $deep = true) {

    // find by multiple values
    if(is_array($value) && count($value) > 1) {
      $result = array();
      foreach($value as $arg) {
        if($page = $this->findBy($key, $arg)) $result[$page->uri()] = $page;
      }      
      return (empty($result)) ? null : new KirbyPages($result);
    } else if(is_array($value)) {
      // reduce the array of values to a single value
      $value = $value[0];
    }
        
    $found      = false;
    $collection = $this->toArray(); 

    // go through the immediate children and all children of children
    while($found == false) {

      $next = array();

      // go through all items in the collection and search for the value
      foreach($collection as $item) {
        // if a result has been found, return that result
        if($item->$key() == $value) return $item;
        // otherwise collect all children 
        $next = array_merge($next, $item->children()->_);
      }

      if(!$deep || empty($next)) return false;
      $collection = $next;

    }

    // find by a single item
    $index = $this->index();
    foreach($index as $page) if($value == $page->$key()) return $page;
    return null;        
  
  }

  /**
   * Finds the currently open page in this collection if available
   * 
   * @return mixed KirbyPage or null
   */
  public function findOpen() {
    return $this->findBy('isOpen', true, false);
  }

  /**
   * Finds a single page by its UID
   * Pass multiple UIDs as separate arguments to get a KirbyPages collection with all matches
   *
   * @param list Either a single UID or multiple UIDs as a list of arguments
   * @return mixed KirbyPage, KirbyPages or null
   */
  public function findByUID() {
    $value = func_get_args();
    return $this->findBy('uid', $value);
  }

  /**
   * Finds a single page by its dirname
   * Pass multiple dirnames as separate arguments to get a KirbyPages collection with all matches
   *
   * @param list Either a single dirname or multiple dirnames as a list of arguments
   * @return mixed KirbyPage, KirbyPages or null
   */
  public function findByDirname() {
    $value = func_get_args();
    return $this->findBy('dirname', $value);
  }
  
  /**
   * Finds a single page by its title
   * Pass multiple titles as separate arguments to get a KirbyPages collection with all matches
   *
   * @param list Either a single title or multiple titles as a list of arguments
   * @return mixed KirbyPage, KirbyPages or null
   */
  public function findByTitle() {
    $value = func_get_args();
    return $this->findBy('title', $value);
  }

  /**
   * Finds a single page by its hash
   * Pass multiple hashes as separate arguments to get a KirbyPages collection with all matches
   *
   * @param list Either a single hash or multiple hashes as a list of arguments
   * @return mixed KirbyPage, KirbyPages or null
   */
  public function findByHash() {
    $value = func_get_args();
    return $this->findBy('hash', $value);
  }

  /**
   * Returns only visible pages from this set of pages
   *
   * @return object KirbyPages
   */
  public function visible() {
    if(!is_null($this->visible)) return $this->visible;
    return $this->visible = $this->filterBy('isVisible', true);
  }

  /**
   * Counts visible pages in this set
   *
   * @return int
   */
  public function countVisible() {
    return $this->visible()->count();  
  }

  /**
   * Returns only invisible pages from this set of pages
   *
   * @return object KirbyPages
   */
  public function invisible() {
    if(!is_null($this->invisible)) return $this->invisible;
    return $this->invisible = $this->filterBy('isVisible', false);
  }
    
  /**
   * Counts invisible pages in this set
   *
   * @return int
   */
  public function countInvisible() {
    return $this->invisible()->count();  
  }
 
  /**
   * Sorts all pages in this set by one of its fields
   *
   * @param string $field
   * @param string $direction
   * @param mixed $method
   * @return object KirbyPages
   */
  public function sortBy($field, $direction='asc', $method=SORT_REGULAR) {

    if($field == 'dirname') $method = 'natural';
        
    $pages = a::sort($this->_, $field, $direction, $method);
    
    return new KirbyPages($pages);

  }

  /**
   * Returns a simple list of links for all pages in this collection
   * This is perfect for simple debugging
   *
   * @return string 
   */
  public function __toString() {
    $output = array();
    foreach($this->toArray() as $key => $page) {
      $output[] = $page . '<br />';          
    }    
    return implode(PHP_EOL, $output);
  }

}