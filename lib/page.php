<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Page
 *
 * This class represents a single page of 
 * a Kirby CMS powered website.
 * A page is derived from a subfolder of the content folder.
 * A page can have unlimited subpages (children)
 * and attached media files. Its custom data is fetched from
 * a text file with separated fields.  
 * 
 * @package Kirby CMS
 */
class KirbyPage {

  // the full root of the directory of this page
  protected $root = null;

  // cache for the dir object 
  protected $dir = null;

  // cache for the children collection
  protected $children = null;

  // cache for all found siblings
  protected $siblings = null;

  // cache for the parent KirbyPage object
  protected $parent = null;
  
  // the relative url 
  protected $uri = null;

  // cache for the title of this page
  protected $title = null;
  
  // cache for this page's hash
  protected $hash = null;
  
  // cache for the found template
  protected $template = null;

  // cache for the intended template
  protected $intendedTemplate = null;
  
  // cache for the KirbyFiles collection
  protected $files = null;
  
  // cache for the active state of this page
  protected $isActive = null;
  
  // cache for the open state of this page
  protected $isOpen = null;
  
  // cache for the main content file
  protected $content = null;

  /**
   * Constructor
   * 
   * @param string $root The full root to this page's directory
   */
  public function __construct($root) {
    $this->root = realpath($root);
  } 
  
  // directory related methods

  /**
   * Returns the full directory path
   * 
   * @return string i.e. /var/www/kirby/content/01-projects
   */
  public function root() {
    return $this->root;
  }

  /** 
   * Returns a directory object with all info 
   * about the related content directory
   * 
   * @return object KirbyDir
   */  
  public function dir() {
    return (!is_null($this->dir)) ? $this->dir : new KirbyDir($this->root());                
  }      

  /** 
   * Returns the optional prepended
   * sorting number from the folder name 
   * 
   * @return string i.e. 01-projects returns 01
   */
  public function num() {
    return $this->dir()->num();
  }

  /** 
   * Returns the unique id
   * The unique id is the folder name without 
   * prepended sorting number.
   * 
   * @return string i.e. 01-projects returns projects
   */
  public function uid() {
    return $this->dir()->uid();
  }

  /**
   * Returns the original directory name 
   * where all the data of the page is stored. 
   * 
   * @return string 
   */
  public function dirname() {
    return $this->dir()->name();
  }  

  /**
   * Returns the relative directory path
   * excluding the document root
   * 
   * @return string i.e. content/01-projects
   */
  public function diruri() {
    return $this->dir()->uri();
  }

  /**
   * Returns the unix timestamp of the last modification 
   * date of this page. This can be used for cache invalidation.
   * 
   * @return int
   */
  public function modified() {
    return $this->dir()->modified();
  }

  // URL methods

  /** 
   * Returns the full url for the page
   * 
   * @return string
   */
  public function url() {

    // Kirby is trying to remove the home folder name from the url
    // unless you set the home.keepurl option to true. 
    if($this->isHomePage() && !c::get('home.keepurl')) {
      return url();                    
    } else {
      return url($this->uri());
    }
  
  }

  /** 
   * Setter and getter for the uri
   * The uri is the page's url path without the base url
   * 
   * @param  string $uri this is used to set the uri in context with other pages
   * @return string i.e. projects/web/project-a
   */
  public function uri($uri = null) {
    if(!is_null($uri)) return $this->uri = ltrim($uri, '/');
    return (empty($this->uri)) ? $this->uid() : $this->uri;
  }

  /** 
   * Returns a tiny url for the page
   * which is built with the unique hash
   * and the tinyurl.folder defined in the config
   * 
   * @return string i.e. http://yourdomain.com/x/abcd
   */
  public function tinyurl() {
    return (c::get('tinyurl.enabled')) ? url(c::get('tinyurl.folder') . '/' . $this->hash()) : $this->url(false);    
  }

  // Getters

  /** 
   * Returns the title of the page
   * The title is being fetched from the text file
   * If no title field exists in the text fiel or
   * the title is empty, the uid will be used as title.
   * 
   * @return string
   */
  public function title() {
    if(!is_null($this->title)) return $this->title;
    return $this->title = $this->content() && $this->content()->title() ? $this->content()->title() : $this->uid();
  }

  /**
   * Returns a unique hashed version of the uri,
   * which is used for the tinyurl for example
   * 
   * @return string
   */
  public function hash() {
    if(!is_null($this->hash)) return $this->hash;

    // add a unique hash
    $checksum = sprintf('%u', crc32($this->uri()));
    return $this->hash = base_convert($checksum, 10, 36);
  }

  /**
   * Returns a numeric indicator how deep the page is nested
   * 0 = site, 1 = first level, 2 = second level, etc. 
   * 
   * @return int
   */
  public function depth() {
    $parent = $this->parent();
    return ($parent && !is_a($parent, 'KirbySite')) ? ($parent->depth() + 1) : 1;
  }

  // template stuff

  /**
   * Returns the name of the used template
   * The name of the template is defined by the name
   * of the content text file. 
   * 
   * i.e. text file: project.txt / template name: project
   * 
   * This method returns the name of the default template
   * if there's no template with such a name
   * 
   * @return string
   */
  public function template() {
    
    if(!is_null($this->template)) return $this->template;

    $templateName = $this->intendedTemplate();
    $templateFile = c::get('root.templates') . DS . $templateName . '.php';

    return $this->template = (file_exists($templateFile)) ? $templateName : c::get('tpl.default');
  
  }

  /**
   * Returns the full path to the used template file
   * 
   * @return string
   */
  public function templateFile() {
    return c::get('root.templates') . DS . $this->template() . '.php';
  }

  /**
   * Returns the name of the content text file / intended template
   * So even if there's no such template it will return the intended name.
   * 
   * @return string
   */
  public function intendedTemplate() {
    
    if(!is_null($this->intendedTemplate)) return $this->intendedTemplate;

    // with language support on, filenames need some extra
    // treatment since the language codes in the content filenames 
    // can mess up the intended template. 
    if(c::get('lang.support')) {
      
      if($content = $this->defaultContent()) {
        return $this->intendedTemplate = preg_replace('!\.(' . implode('|', c::get('lang.available')) . ')$!i', '', $content->name());
      } else {
        return $this->intendedTemplate = c::get('tpl.default');
      }

    }

    // without language support, it's all nice and easy. 
    return $this->intendedTemplate = ($this->content()) ? $this->content()->name() : c::get('tpl.default');
  
  }

  /**
   * Returns the full path to the intended template file
   * This template file may not exist.
   * 
   * @return string
   */
  public function intendedTemplateFile() {
    return c::get('root.templates') . DS . $this->intendedTemplate() . '.php';
  }

  /**
   * Checks if there's a dedicated template for this page
   * Will return false when the default template is used
   *
   * @return boolean
   */
  public function hasTemplate() {
    return ($this->intendedTemplate() == $this->template()) ? true : false;
  }

  // attachments 

  /**
   * Returns the KirbyFiles collection object
   * with all files stored with the current page.
   * 
   * @return object KirbyFiles 
   */
  public function files() {
    //if(!is_null($this->files)) return $this->files;    
    return $this->files = new KirbyFiles($this);
  }

  /**
   * Checks if there are any files stored with this page
   * 
   * @return boolean true = has files, false = no files
   */
  public function hasFiles() {
    return ($this->files()->count() > 0) ? true : false;
  }

  /**
   * Only returns images stored with this page
   * 
   * @return object KirbyFiles
   */
  public function images() {
    return $this->files()->images();
  }

  /**
   * Checks if there are any images stored with this page
   * 
   * @return boolean true = has images, false = no images
   */
  public function hasImages() {
    return $this->files()->hasImages();
  }

  /**
   * Only returns videos stored with this page
   * 
   * @return object KirbyFiles
   */
  public function videos() {
    return $this->files()->videos();
  }

  /**
   * Checks if there are any videos stored with this page
   * 
   * @return boolean true = has videos, false = no videos
   */
  public function hasVideos() {
    return $this->files()->hasVideos();
  }

  /**
   * Only returns documents stored with this page
   * 
   * @return object KirbyFiles
   */
  public function documents() {
    return $this->files()->documents();
  }

  /**
   * Checks if there are any documents stored with this page
   * 
   * @return boolean true = has documents, false = no documents
   */
  public function hasDocuments() {
    return $this->files()->hasDocuments();
  }

  /**
   * Only returns sound files stored with this page
   * 
   * @return object KirbyFiles
   */
  public function sounds() {
    return $this->files()->sounds();
  }

  /**
   * Checks if there are any sound files stored with this page
   * 
   * @return boolean true = has sound files, false = no sound files
   */
  public function hasSounds() {
    return $this->files()->hasSounds();
  }

  /**
   * Returns all other files stored with this page
   * 
   * @return object KirbyFiles
   */
  public function others() {
    return $this->files()->others();
  }

  /**
   * Checks if there are any other files stored with this page
   * 
   * @return boolean true = has other files, false = no other files
   */
  public function hasOthers() {
    return $this->files()->hasOthers();
  }

  /**
   * Returns all meta files stored with this page
   * Meta files are text files connected to images, videos, etc. 
   * to provide meta data for those files. 
   * 
   * @return object KirbyFiles
   */
  public function metas() {
    return $this->files()->metas();
  }

  /**
   * Checks if there are any meta files stored with this page
   * 
   * @return boolean true = has meta files, false = no meta files
   */
  public function hasMetas() {
    return $this->files()->hasMetas();
  }

  /**
   * Returns all content files stored with this page
   * Content files are text files with the main content.
   * There can be a single content file for single language websites, 
   * or multiple content text files for multi-language sites.
   * 
   * @return object KirbyFiles
   */
  public function contents() {
    return $this->files()->contents();
  }

  /**
   * Checks if there are any content files stored with this page
   * 
   * @return boolean true = has content files, false = no content files
   */
  public function hasContents() {
    return $this->files()->hasContents();
  }

  // Main content

  /**
   * Returns the main content file
   * which will be used to fetch custom variables for the page.
   * 
   * @return object KirbyContent
   */
  public function content($lang = null) {

    // multi-language handling
    if(c::get('lang.support')) {

      // initiate the cache if not done yet
      if(is_null($this->content) || !is_array($this->content)) $this->content = array();

      // get the current applicable language code
      $lang = (is_null($lang)) ? c::get('lang.current') : $lang;

      // in cache? 
      if(isset($this->content[(string)$lang])) return $this->content[$lang];

      // find the matching content file, store and return it
      $content = $this->contents()->filterBy('languageCode', $lang)->first();

      // fall back to the default language
      if(!$content) $content = $this->defaultContent();
    
      // store and return the content
      return $this->content[$lang] = $content;

    }

    // single language handling
    if(!is_null($this->content)) return $this->content;
    return $this->content = $this->contents()->first();

  }

  /**
   * Returns the default content 
   * for multi-language support
   * 
   * @return object KirbyContent
   */
  public function defaultContent() {
    return $this->content(c::get('lang.default'));
  }

  // Traversing

  /**
   * Returns the parent page object
   * 
   * @return object KirbyPage
   */
  public function parent() {
    
    // only fetch the parent object once
    if(!is_null($this->parent)) return $this->parent;
    
    $parentURI = dirname($this->uri());
    $parentURI = trim($parentURI, '.');

    // fetch the parent object by the parent uri
    return $this->parent = ($parentURI) ? site()->pages()->find($parentURI) : site();

  }

  /**
   * Returns a KirbyPages collection with all 
   * parents of this page until the first level of pages.
   * 
   * @return object KirbyPages
   */
  public function parents() {

    $parents = array();
    $next    = $this->parent();

    while($next->depth() > 0) {
      $parents[$next->uri()] = $next;
      $next = $next->parent();
    }

    return new KirbyPages($parents);

  }

  /**
   * Checks if the page is a child of the given page
   * 
   * @param object KirbyPage the page object to check
   * @return boolean
   */
  public function isChildOf(KirbyPage $page) {
    if($this->equals($page)) return false;
    return ($this->parent()->equals($page));
  }

  /**
   * Checks if the page is a descendant of the given page
   * 
   * @param object KirbyPage the page object to check
   * @return boolean
   */
  public function isDescendantOf(KirbyPage $page) {
    
    if($this->equals($page)) return false;

    $parent = $this;

    while($parent = $parent->parent()) {
      if($parent->equals($page)) return true;
    } 
    
    return false;

  }

  /**
   * Checks if the page is a descendant of the currently active page
   * 
   * @return boolean
   */
  public function isDescendantOfActive() {
    $active = site()->activePage();
    if(!$active) return false;
    return $this->isDescendantOf($active);
  }

  /**
   * Checks if the page is an ancestor of the given page
   * 
   * @param object KirbyPage the page object to check
   * @return boolean
   */
  public function isAncestorOf($page) {
    return $page->isDescendantOf($this);
  }

  /**
   * Returns all subpages/children for the page
   * 
   * @return object KirbyPages
   */
  public function children() {
    
    // cache the set of children
    if(!is_null($this->children)) return $this->children;
    
    // fetch all children for this page
    return $this->children = new KirbyPages($this);
    
  }

  /**
   * Counts all children of this page
   * 
   * @return int
   */
  public function countChildren() {
    return $this->children()->count();
  }

  /**
   * Checks if this page has children
   * 
   * @return boolean 
   */
  public function hasChildren() {
    return ($this->countChildren() > 0) ? true : false;    
  }

  /**
   * Returns all siblings of this page
   * 
   * @return object KirbyPages 
   */
  public function siblings() {
    // cache the set of siblings
    if(!is_null($this->siblings)) return $this->siblings;
    
    // TODO: replace this->root by this->uri
    return $this->siblings = $this->parent()->children()->not($this->root());
  }

  /**
   * Counts the number of siblings for this page
   * 
   * @return int
   */
  public function countSiblings() {
    return $this->siblings()->count();  
  }

  /**
   * Checks if this page has siblings
   * 
   * @return boolean
   */
  public function hasSiblings() {
    return ($this->countSiblings() > 0) ? true : false;      
  }

  /**
   * Internal method to return the next page
   * 
   * @param object $siblings KirbyPages A collection of siblings to search in
   * @param string $sort An optional sort field for the siblings
   * @param string $direction An optional sort direction  
   * @return mixed KirbyPage or null
   */
  protected function _next(KirbyPages $siblings, $sort = false, $direction = 'asc') {
    if($sort) $siblings = $siblings->sortBy($sort, $direction);
    $index = $siblings->indexOf($this);
    if($index === false) return false;
    $siblings  = array_values($siblings->toArray());
    $nextIndex = $index+1;
    return a::get($siblings, $nextIndex, null);                  
  }

  /**
   * Internal method to return the previous page
   * 
   * @param object $siblings KirbyPages A collection of siblings to search in
   * @param string $sort An optional sort field for the siblings
   * @param string $direction An optional sort direction  
   * @return mixed KirbyPage or null
   */
  protected function _prev(KirbyPages $siblings, $sort = false, $direction = 'asc') {
    if($sort) $siblings = $siblings->sortBy($sort, $direction);
    $index = $siblings->indexOf($this);
    if($index === false) return false;
    $siblings  = array_values($siblings->toArray());
    $prevIndex = $index-1;
    return a::get($siblings, $prevIndex, null);                
  }

  /**
   * Returns the next page in the current collection if available
   * 
   * @param string $sort An optional sort field for the siblings
   * @param string $direction An optional sort direction  
   * @return mixed KirbyPage or null  
   */
  public function next($sort = false, $direction = 'asc') {
    return $this->_next($this->siblings(), $sort, $direction);
  }
  
  /**
   * Returns the next visible page in the current collection if available
   * 
   * @param string $sort An optional sort field for the siblings
   * @param string $direction An optional sort direction  
   * @return mixed KirbyPage or null  
   */
  public function nextVisible($sort = false, $direction = 'asc') {
    return $this->_next($this->siblings()->visible(), $sort, $direction);    
  }
  
  /**
   * Checks if there is a next page in the collection
   * 
   * @param string $sort An optional sort field for the siblings
   * @param string $direction An optional sort direction  
   * @return boolean
   */
  public function hasNext($sort = false, $direction = 'asc') {
    return ($this->next($sort, $direction)) ? true : false;   
  }

  /**
   * Checks if there is a next visible page in the collection
   * 
   * @param string $sort An optional sort field for the siblings
   * @param string $direction An optional sort direction  
   * @return boolean
   */
  public function hasNextVisible($sort = false, $direction = 'asc') {
    return ($this->nextVisible($sort, $direction)) ? true : false;   
  }
  
  /**
   * Returns the previous page in the current collection if available
   * 
   * @param string $sort An optional sort field for the siblings
   * @param string $direction An optional sort direction  
   * @return mixed KirbyPage or null  
   */
  public function prev($sort = false, $direction = 'asc') {
    return $this->_prev($this->siblings(), $sort, $direction);
  }

  /**
   * Returns the previous visible page in the current collection if available
   * 
   * @param string $sort An optional sort field for the siblings
   * @param string $direction An optional sort direction  
   * @return mixed KirbyPage or null  
   */
  public function prevVisible($sort = false, $direction = 'asc') {
    return $this->_prev($this->siblings()->visible(), $sort, $direction);
  }
  
  /**
   * Checks if there is a previous page in the collection
   * 
   * @param string $sort An optional sort field for the siblings
   * @param string $direction An optional sort direction  
   * @return boolean
   */
  public function hasPrev($sort = false, $direction = 'asc') {
    return ($this->prev($sort, $direction)) ? true : false; 
  }

  /**
   * Checks if there is a previous visible page in the collection
   * 
   * @param string $sort An optional sort field for the siblings
   * @param string $direction An optional sort direction  
   * @return boolean
   */
  public function hasPrevVisible($sort = false, $direction = 'asc') {
    return ($this->prevVisible($sort, $direction)) ? true : false; 
  }

  // state checkers

  /**
   * Checks if a page exists
   * 
   * @return boolean
   */
  public function exists() {
    return is_dir($this->root()) ? true : false;
  }

  /**
   * Checks if this page is visible
   * A visible page has a prepended number 
   * in its foldername. i.e. 01-projects
   * 
   * @return boolean
   */
  public function isVisible() {
    return !is_null($this->num());
  }

  /**
   * Checks if this page is invisible
   * An invisible page has no prepended number 
   * in its foldername. i.e. projects
   * 
   * @return boolean
   */
  public function isInvisible() {
    return !$this->visible();
  }

  /**
   * Checks if this page is the home page
   * You can define the uri of the homepage in your config
   * file with the home option. By default it's assumed
   * that the homepage folder has the name "home"
   * 
   * @return boolean
   */
  public function isHomePage() {
    return ($this->uri() === c::get('home')) ? true : false;    
  }

  /**
   * Checks if this page is the error page
   * You can define the uri of the error page in your config
   * file with the error option. By default it's assumed
   * that the error page folder has the name "error"
   * 
   * @return boolean
   */
  public function isErrorPage() {
    return ($this->uri() === c::get('404')) ? true : false;    
  }

  /**
   * Compares this page with a given page object
   * 
   * @param object $page A KirbyPage object to compare
   * @return boolean
   */
  public function equals(KirbyPage $page) {
    return ($page === $this) ? true : false;
  }

  /**
   * Checks if this page is active.
   * This means that the user is currently browsing this page.
   * 
   * @return boolean
   */
  public function isActive() {
    if(!is_null($this->isActive)) return $this->isActive;
    return $this->isActive = site()->activePage()->equals($this); 
  }

  /**
   * Checks if this page is open.
   * This means that the user is currently browsing this page
   * or one of its subpages.
   * 
   * @return boolean
   */
  public function isOpen() {

    if(!is_null($this->isOpen)) return $this->isOpen;
    
    if($this->isActive()) return true;

    $u = array_values(site()->uri()->path()->toArray());
    $p = str::split($this->uri(), '/');

    for($x=0; $x<count($p); $x++) {
      if(a::get($p, $x) != a::get($u, $x)) return $this->isOpen = false;
    }

    return $this->isOpen = true;
  
  }
 
  // magic getters

  /**
   * Enables getter function calls for custom fields
   * i.e. $page->title()
   * 
   * @param string $key this is auto-filled by PHP with the called method name
   * @return mixed
   */
  public function __call($key, $arguments = null) {    
    
    $content = ($this->content()) ? $this->content()->$key() : null;
  
    if($content && $key == 'date') {
      $content = (!empty($arguments)) ? $content->toDate(a::first($arguments)) : $content->toTimestamp();
    }

    return $content;

  }

  /**
   * Enables pseudo attributes for custom fields
   * i.e. $page->title
   * 
   * @param string $key this is auto-filled by PHP with the called attribute name
   * @return mixed
   */
  public function __get($key) {

    $methods = array(
      'root',
      'dir',
      'uri',
      'uid',
      'title',
      'hash',
      'template',
      'intendedTemplate',      
      'content',
    );

    // legacy code to enable getters like $page->uid
    if(in_array($key, $methods)) {
      return $this->$key();      
    } else {

      $content = ($this->content()) ? $this->content()->$key() : null;
      
      // legacy to get the timestamp with $page->date;
      if($content && $key == 'date') {
        $content = $content->toTimestamp();
      }
      
      return $content;
    
    }

  }

  /**
   * This makes it possible to simply echo the entire page object
   * and get a usable result for debugging
   * 
   * @return string
   */
  public function __toString() {
    return '<a href="' . $this->url() . '">' . $this->url() . '</a>';  
  }

}