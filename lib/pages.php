<?php 

namespace Kirby\CMS;

use Kirby\Toolkit\A;
use Kirby\Toolkit\C;
use Kirby\Toolkit\Collection;
use Kirby\Toolkit\Str;

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Pages
 * 
 * This is the main collection object 
 * for a set of Page objects
 * 
 * @package   Kirby CMS
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://getkirby.com/license
 */
class Pages extends Collection {

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
   * @param mixed $input Can either be a Page object (All children will be auto-added) or an array of Page objects
   */
  public function __construct($input = array()) {

    if(is_a($input, 'Kirby\\CMS\\Page')) {

      $uri = is_a($input, 'Kirby\\CMS\\Site')  ? '' : $input->uri();

      foreach($input->dir()->children() as $dir) {
        
        $child = new Page($dir);
        $child->parent($input);
        
        $this->set($child->id(), $child);
      
      }

    } else if(is_array($input)) {

      foreach($input as $page) {  
        
        if(!is_a($page, 'Kirby\\CMS\\Page')) raise('All pages in a set of Pages have to be Page objects');
        
        // add the page to the collection
        $this->data['_' . $page->uri()] = $page;
      
      }

    } else {
      raise('Pages must be constructed with a Page object or an array of Pages');
    }

  }

  /**
   * Creates a clean one-level array with all 
   * pages, subpages, subsubpages, etc.
   *
   * @param object Pages object for recursive indexing
   * @return array
   */
  public function index(Pages $obj=null) {
    
    if(is_null($obj)) {
      if(!is_null($this->index)) return $this->index;
      $obj = $this;
    }

    foreach($obj->toArray() as $key => $page) {
      $this->index[$page->id()] = $page;
      $this->index($page->children());
    }
    
    return $this->index;
            
  }

  /**
   * Merges all children of all pages
   *
   * @return object Pages
   */
  public function children() {

    if(!is_null($this->children)) return $this->children;

    $children = array();

    foreach($this->toArray() as $page) {
      foreach($page->children() as $child) {
        $children[$child->uri()] = $child;  
      }      
    }

    return $this->children = new Pages($children);

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
   * @return object Page
   */
  public function active() {
    return site::instance()->activePage();
  }

  /**
   * Returns a new collection of pages without the given pages
   * 
   * @param args any number of uris or page elements, passed as individual arguments
   * @return object a new collection without the pages
   */      
  public function not() {
    $args = func_get_args();
    $self = clone $this;
    foreach($args as $kill) {
      if(is_a($kill, 'Page')) {
        unset($self->data['_' . $kill->uri()]);
      } else {
        unset($self->data['_' . $kill]);
      }
    }
    return $self;
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
   * In this case a Pages object will be returned
   *
   * @param list Either a single URI as first argument or multiple URIs as a list of arguments. 
   * @return mixed Either a Page object, a Pages object for multiple pages or null if nothing could be found
   */
  public function find() {
    $args = func_get_args();
    return $this->findByURI($args, 'uid');  
  }

  /**
   * Finds pages by it's unique URI
   *
   * @param mixed $uri Either a single URI string or an array of URIs 
   * @param string $use The field, which should be used (uid or slug)
   * @return mixed Either a Page object, a Pages object for multiple pages or null if nothing could be found
   */
  public function findByURI($uri, $use = 'uid') {

    // find multiple pages by uri 
    if(is_array($uri) && count($uri) > 1) {
      $result = array();
      foreach($uri as $u) {
        if($page = $this->findByURI($u)) $result[$page->id()] = $page;
      }      
      return (empty($result)) ? null : new Pages($result);
    } else if(is_array($uri)) {
      // reduce the array of values to a single value
      $uri = a::first($uri);
    }

    // find a single page by uri
    $path  = $uri;      
    $array = str::split($uri, '/');
    $obj   = $this;
    $page  = false;

    foreach($array as $p) {    

      $next = $obj->findBy($use, $p, false);

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
   * @param mixed $value The value to match against. Array: the method will search for multiple values and return a Pages collection of results. 
   * @param boolean $deep true: the method will search all children, grantchildren, etc., false: the method will only search the current set
   * @return mixed Page if a single page is found, Pages if multiple values have been passed and multiple pages are found or null if nothing could be found
   */
  public function findBy($key, $value, $deep = true) {

    // find by multiple values
    if(is_array($value) && count($value) > 1) {
      $result = array();
      foreach($value as $arg) {
        if($page = $this->findBy($key, $arg)) $result[$page->id()] = $page;
      }      
      return (empty($result)) ? null : new Pages($result);
    } else if(is_array($value)) {
      // reduce the array of values to a single value
      $value = a::first($value);
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
        $next = array_merge($next, $item->children()->data);
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
   * @return mixed Page or null
   */
  public function findOpen() {
    return $this->findBy('isOpen', true, false);
  }

  /**
   * Finds a single page by its UID
   * Pass multiple UIDs as separate arguments to get a Pages collection with all matches
   *
   * @param list Either a single UID or multiple UIDs as a list of arguments
   * @return mixed Page, Pages or null
   */
  public function findByUID() {
    $value = func_get_args();
    return $this->findBy('uid', $value);
  }

  /**
   * Finds a single page by its dirname
   * Pass multiple dirnames as separate arguments to get a Pages collection with all matches
   *
   * @param list Either a single dirname or multiple dirnames as a list of arguments
   * @return mixed Page, Pages or null
   */
  public function findByDirname() {
    $value = func_get_args();
    return $this->findBy('dirname', $value);
  }
  
  /**
   * Finds a single page by its title
   * Pass multiple titles as separate arguments to get a Pages collection with all matches
   *
   * @param list Either a single title or multiple titles as a list of arguments
   * @return mixed Page, Pages or null
   */
  public function findByTitle() {
    $value = func_get_args();
    return $this->findBy('title', $value);
  }

  /**
   * Finds a single page by its hash
   * Pass multiple hashes as separate arguments to get a Pages collection with all matches
   *
   * @param list Either a single hash or multiple hashes as a list of arguments
   * @return mixed Page, Pages or null
   */
  public function findByHash() {
    $value = func_get_args();
    return $this->findBy('hash', $value);
  }

  /**
   * Returns only visible pages from this set of pages
   *
   * @return object Pages
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
   * @return object Pages
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
   * @return object Pages
   */
  public function sortBy($field, $direction = 'asc', $method = SORT_REGULAR) {

    if($field == 'dirname') $method = 'natural';
        
    $pages = a::sort($this->data, $field, $direction, $method);
    
    return new Pages($pages);

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

  /**
   * Returns a more readable dump array for the dump() helper
   * 
   * @return array
   */
  public function __toDump() {

    $dump = array(
      'count' => array(
        'total'     => $this->count(),
        'visible'   => $this->countVisible(),
        'invisible' => $this->countInvisible(),
      ),
      'pages' => array(),
    );

    foreach($this->toArray() as $page) {
      $dump['pages'][] = $page->diruri();
    }

    return $dump;

  }

  /**
   * Tries to find the index number for the given element
   * 
   * @param  mixed $needle the element to search for
   * @return mixed the name of the key or false
   */      
  public function indexOf($needle) {
    return array_search('_' . $needle->id(), array_keys($this->data));
  }
  
  /**
   * Merges multiple collections of pages
   * 
   * <code>
   * 
   * $articles = page('blog')->children();
   * $news     = page('news')->children();
   * $merged   = pages::merge($articles, $news);
   * // $merged is now a new collection with all children from news and blog
   * 
   * </code>
   * 
   * @param args n sets of pages
   * @return object A new pages Collection 
   */
  static function merge() {
    
    $objs   = func_get_args();
    $result = array();
    
    foreach($objs as $obj) {    
      foreach($obj as $key => $page) {
        $result[$key] = $page;
      }
    }

    return new Pages($result);    
      
  }

}