<?php

namespace Kirby\CMS;

use Kirby\Toolkit\A;
use Kirby\Toolkit\C;
use Kirby\Toolkit\Str;
use Kirby\Toolkit\Template;
use Kirby\CMS\Page\Dir;
use Kirby\CMS\Page\Cache;

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
 * @package   Kirby CMS
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://getkirby.com/license
 */
class Page {

  // the full root of the directory of this page
  protected $root = null;

  // cache for the dir object 
  protected $dir = null;

  // cache for the children collection
  protected $children = null;

  // cache for all found siblings
  protected $siblings = null;

  // cache for the parent Page object
  protected $parent = null;
  
  // the unique id for each page 
  protected $id = null;

  // cache for the title of this page
  protected $title = null;
  
  // cache for this page's hash
  protected $hash = null;
  
  // cache for the found template
  protected $template = null;

  // cache for the intended template
  protected $intendedTemplate = null;
  
  // cache for the Files collection
  protected $files = null;
  
  // cache for the active state of this page
  protected $isActive = null;
  
  // cache for the open state of this page
  protected $isOpen = null;
  
  // cache for the main content file
  protected $content = null;

  // cache for the default content in multi-lang sites
  protected $defaultContent = null;

  // page object extensions
  static protected $extensions = array();

  /**
   * Constructor
   * 
   * @param mixed $root The full root to this page's directory or a page object which this should be converted from
   */
  public function __construct($root) {
    if(is_a($root, 'Kirby\\CMS\\Page')) {
      $this->root   = $root->root();
      $this->id     = $root->id();
      $this->parent = $root->parent();
    } else {
      $this->root = realpath($root);
      $this->id   = md5($this->root);  
    } 
  } 
  
  /**
   * Rests all cached variables
   */
  public function reset() {  

    // keep the parent object and the root dir
    $keep = array('parent', 'root');

    // reset all cached attributes so they will get fetched again
    foreach(get_object_vars($this) as $key => $val) {
      if(in_array($key, $keep)) continue;
      $this->$key = null;
    }

    // reset the id
    $this->id = md5($this->root);

    // reset all parent pages
    if(!$this->isSite()) $this->parent()->reset();

  }

  // directory related methods

  /**
   * The unique id for this page
   * This is generated on construction by 
   * md5-ing the root of the page
   * 
   * @return string
   */
  public function id() {
    return $this->id;
  }

  /**
   * Returns the full directory path
   * 
   * @return string i.e. /var/www/kirby/content/01-projects
   */
  public function root() {
    return $this->root;
  }

  /** 
   * Returns a dir object with all info 
   * about the related content dir
   * 
   * @return object PageDir
   */  
  public function dir() {
    return (!is_null($this->dir)) ? $this->dir : new Dir($this->root());                
  }      

  /** 
   * Returns the optional prepended
   * sorting number from the folder name 
   * 
   * If you pass a number this can also be used
   * to change the page's number on the file system
   * Be careful with that!!
   * 
   * @param int $num Optional way to use this as a setter
   * @return string i.e. 01-projects returns 01
   */
  public function num($num = null) {

    // change the current num
    if(!is_null($num)) {

      // creat the new directory root
      $dir = dirname($this->dir()) . DS . $num . '-' . $this->uid();
      
      // try to move the directory
      if(!\Kirby\Toolkit\dir::move($this->root(), $dir)) raise('The directory could not be moved', 'move-failed');

      // change the root and reset the page object
      $this->root = $dir;
      $this->reset();

      return $this;

    }

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
   * Returns the slug for the page
   * The slug is the last part of the URL path
   * For multilang sites this can be translated with a URL-Key field 
   * in the text file for this page. 
   * 
   * @param string $lang Optional language code to get the translated slug
   * @return string i.e. 01-projects returns projects
   */
  public function slug($lang = null) {

    // get the slug for a different language code 
    // than the currently active language
    if(site::$multilang) {      

      $curr = c::get('lang.current');

      if(is_null($lang)) $lang = $curr;

      // get the slug for the default language
      if($lang == c::get('lang.default')) {
        return $this->dir()->uid();        

      // get the slug for the current language
      } else if($lang == $curr) {

        // try to find a translation for the slug
        $key = (string)$this->url_key();
        
        // return the translated slug or otherwise the uid
        return (empty($key)) ? $this->dir()->uid() : $key;              
      
      // get the translated slug
      } else {

        // search for content in the specified language
        if($content = $this->content($lang)) {            
          // search for a translated url_key in that language
          if($slug = $content->url_key()) {
            // if available, use the translated url key as slug
            return str::slug($slug);
          }
        } 

        // use the uid if no translation could be found
        return $this->dir()->uid();

      }

    } else {

      // simply return the uid of the directory and use that as slug
      return $this->dir()->uid();

    }

  }
  
  /** 
   * Returns the full url for the page
   * 
   * @param string $lang Optional language code to get the URL for that specific language on multilang sites
   * @return string
   */
  public function url($lang = null) {

    // for multi language sites every url needs
    // to be treated specially to make sure each uid is translated properly
    // and language codes are prepended if needed
    if(site::$multilang && is_null($lang)) {
      // get the current language
      $lang = site::instance()->language()->code();
    } 

    // Kirby is trying to remove the home folder name from the url
    // unless you set the home.keepurl option to true. 
    if($this->isHomePage() && !c::get('home.keepurl')) {
      // return the base url
      return site::instance()->url($lang);                    
    } else {
      // get the parent page object to inherit the url
      $parent = $this->parent();

      if($parent->isSite() and !$lang) {
        return $parent->indexurl() . '/' . $this->slug();  
      } else if($parent->isHomePage()) {
        return site::instance()->url($lang) . '/' . $parent->uid() . '/' . $this->slug($lang); 
      } else {
        return $this->parent()->url($lang) . '/' . $this->slug($lang);        
      }
    }

  }

  /** 
   * Setter and getter for the uri
   * The uri is the page's url path without the base url
   * 
   * @param string $lang Optional language code to get the URI in other languages
   * @return string i.e. projects/web/project-a
   */
  public function uri($lang = null) {

    // get the parent page object
    $parent = $this->parent();

    if($parent->isSite()) {
      // if the parent page is the site object
      // only use the slug without the site's uri, since that is the entire uri object
      // and not a simple string like on subpages
      return $this->slug($lang);
    } else {
      // build the page's uri with the parent uri and the page's slug
      return $parent->uri($lang) . '/' . $this->slug($lang);
    }

  }

  /** 
   * Returns a tiny url for the page
   * which is built with the unique hash
   * and the tinyurl.folder defined in the config
   * 
   * @return string i.e. http://yourdomain.com/x/abcd
   */
  public function tinyurl() {    
    return tinyurl::create($this);
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
    $checksum = sprintf('%u', crc32($this->uri(c::get('lang.default'))));
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
    return ($parent && !is_a($parent, 'Site')) ? ($parent->depth() + 1) : 1;
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
   * @param string $template Optional template name to overwrite the auto-detected template 
   * @return string
   */
  public function template($template = null) {
  
    // use this method as setter to overwrite the used template
    if(!is_null($template)) return $this->template = $template;

    // check for a cached template name
    if(!is_null($this->template)) return $this->template;

    $templateName = $this->intendedTemplate();
    $templateFile = KIRBY_SITE_ROOT_TEMPLATES . DS . $templateName . '.php';

    return $this->template = (file_exists($templateFile)) ? $templateName : c::get('tpl.default');
  
  }

  /**
   * Returns the full path to the used template file
   * 
   * @return string
   */
  public function templateFile() {
    return KIRBY_SITE_ROOT_TEMPLATES . DS . $this->template() . '.php';
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
    if(site::$multilang) {
      
      if($content = $this->defaultContent()) {
        return $this->intendedTemplate = preg_replace('!\.(' . implode('|', site::instance()->languages()->codes()) . ')$!i', '', $content->name());
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
    return KIRBY_SITE_ROOT_TEMPLATES . DS . $this->intendedTemplate() . '.php';
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

  /**
   * Renames all content files for this page, 
   * so a different template will be chosen for it.
   * 
   * @param string $template The name of the new template
   * @return this
   */
  public function changeTemplate($template) {

    // make sure the template name is a safe name
    $template = str::slug($template);

    // don't change anything, if this is already the current template
    if($template == $this->intendedTemplate()) return true;

    // Check for existing content files
    if(!$this->contents()->count()) raise('This page does not have any content files', 'missing-content');

    // Rename all content files
    foreach($this->contents() as $content) {    
      $content->rename($template);
    }

    $this->reset();

    return $this;

  }

  // attachments 

  /**
   * Returns the Files collection object
   * with all files stored with the current page.
   * 
   * @return object Files 
   */
  public function files() {
    //if(!is_null($this->files)) return $this->files;    
    return $this->files = new Files($this);
  }

  /**
   * Finds a single file by its filename
   * 
   * @param string $filename
   * @return object
   */
  public function file($filename) {
    return $this->files()->find($filename);
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
   * @return object Files
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
   * @return object Files
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
   * @return object Files
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
   * @return object Files
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
   * @return object Files
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
   * @return object Files
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
   * @return object Files
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
   * @return object Content
   */
  public function content($lang = null) {

    // multi-language handling
    if(site::$multilang) {

      // initiate the cache if not done yet
      if(is_null($this->content) || !is_array($this->content)) $this->content = array();

      // get the current applicable language code
      $lang = (is_null($lang)) ? c::get('lang.current') : $lang;

      // in cache? 
      if(isset($this->content[$lang])) return $this->content[$lang];

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
   * @return object Content
   */
  public function defaultContent() {
    if(!is_null($this->defaultContent)) return $this->defaultContent;
    return $this->defaultContent = $this->contents()->filterBy('languageCode', c::get('lang.default'))->first();
  }

  // Traversing

  /**
   * Returns the parent page object
   *
   * @param object $parent Setter for the parent page object 
   * @return object Page
   */
  public function parent(Page $parent = null) {
    
    if(!is_null($parent)) return $this->parent = $parent;

    // only fetch the parent object once
    if(!is_null($this->parent)) return $this->parent;
    
    $parentURI = dirname($this->uri());
    $parentURI = trim($parentURI, '.');

    // fetch the parent object by the parent uri
    return $this->parent = ($parentURI) ? site::instance()->pages()->findBy('uri', $parentURI) : site::instance();

  }

  /**
   * Returns a Pages collection with all 
   * parents of this page until the first level of pages.
   * 
   * @return object Pages
   */
  public function parents() {

    $parents = array();
    $next    = $this->parent();

    while($next->depth() > 0) {
      $parents[$next->uri()] = $next;
      $next = $next->parent();
    }

    return new Pages($parents);

  }

  /**
   * Checks if the page is a child of the given page
   * 
   * @param object Page the page object to check
   * @return boolean
   */
  public function isChildOf(Page $page) {
    if($this->equals($page)) return false;
    return ($this->parent()->equals($page));
  }

  /**
   * Checks if the page is a descendant of the given page
   * 
   * @param object Page the page object to check
   * @return boolean
   */
  public function isDescendantOf(Page $page) {
    
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
    $active = site::instance()->activePage();
    if(!$active) return false;
    return $this->isDescendantOf($active);
  }

  /**
   * Checks if the page is an ancestor of the given page
   * 
   * @param object Page the page object to check
   * @return boolean
   */
  public function isAncestorOf($page) {
    return $page->isDescendantOf($this);
  }

  /**
   * Returns all subpages/children for the page
   * 
   * @return object Pages
   */
  public function children() {
    
    // cache the set of children
    if(!is_null($this->children)) return $this->children;
    
    // fetch all children for this page
    return $this->children = new Pages($this);
    
  }

  /**
   * Shortcut to find subpages of this page
   * 
   * @param string $uri
   * @return object
   */
  public function find($uri) {
    return $this->children()->find($uri);
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
   * Checks if this page has visible children
   * 
   * @return boolean 
   */
  public function hasVisibleChildren() {
    return ($this->children()->visible()->count() > 0) ? true : false;        
  }

  /**
   * Checks if this page has invisible children
   * 
   * @return boolean 
   */
  public function hasInvisibleChildren() {
    return ($this->children()->invisible()->count() > 0) ? true : false;        
  }

  /**
   * Returns all siblings of this page including the current page
   * 
   * @return object Pages 
   */
  public function siblings() {
    // cache the set of siblings
    if(!is_null($this->siblings)) return $this->siblings;    
    return $this->siblings = $this->parent()->children();
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
   * @param object $siblings Pages A collection of siblings to search in
   * @param string $sort An optional sort field for the siblings
   * @param string $direction An optional sort direction  
   * @return mixed Page or null
   */
  protected function _next(Pages $siblings, $sort = false, $direction = 'asc') {
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
   * @param object $siblings Pages A collection of siblings to search in
   * @param string $sort An optional sort field for the siblings
   * @param string $direction An optional sort direction  
   * @return mixed Page or null
   */
  protected function _prev(Pages $siblings, $sort = false, $direction = 'asc') {
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
   * @return mixed Page or null  
   */
  public function next($sort = false, $direction = 'asc') {
    return $this->_next($this->siblings(), $sort, $direction);
  }
  
  /**
   * Returns the next visible page in the current collection if available
   * 
   * @param string $sort An optional sort field for the siblings
   * @param string $direction An optional sort direction  
   * @return mixed Page or null  
   */
  public function nextVisible($sort = false, $direction = 'asc') {
    return $this->_next($this->siblings()->visible(), $sort, $direction);    
  }
  
  /**
   * Returns the next invisible page in the current collection if available
   * 
   * @param string $sort An optional sort field for the siblings
   * @param string $direction An optional sort direction  
   * @return mixed Page or null  
   */
  public function nextInvisible($sort = false, $direction = 'asc') {
    return $this->_next($this->siblings()->invisible(), $sort, $direction);    
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
   * Checks if there is a next invisible page in the collection
   * 
   * @param string $sort An optional sort field for the siblings
   * @param string $direction An optional sort direction  
   * @return boolean
   */
  public function hasNextInvisible($sort = false, $direction = 'asc') {
    return ($this->nextInvisible($sort, $direction)) ? true : false;   
  }

  /**
   * Returns the previous page in the current collection if available
   * 
   * @param string $sort An optional sort field for the siblings
   * @param string $direction An optional sort direction  
   * @return mixed Page or null  
   */
  public function prev($sort = false, $direction = 'asc') {
    return $this->_prev($this->siblings(), $sort, $direction);
  }

  /**
   * Returns the previous visible page in the current collection if available
   * 
   * @param string $sort An optional sort field for the siblings
   * @param string $direction An optional sort direction  
   * @return mixed Page or null  
   */
  public function prevVisible($sort = false, $direction = 'asc') {
    return $this->_prev($this->siblings()->visible(), $sort, $direction);
  }
  
  /**
   * Returns the previous invisible page in the current collection if available
   * 
   * @param string $sort An optional sort field for the siblings
   * @param string $direction An optional sort direction  
   * @return mixed Page or null  
   */
  public function prevInvisible($sort = false, $direction = 'asc') {
    return $this->_prev($this->siblings()->invisible(), $sort, $direction);
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

  /**
   * Checks if there is a previous invisible page in the collection
   * 
   * @param string $sort An optional sort field for the siblings
   * @param string $direction An optional sort direction  
   * @return boolean
   */
  public function hasPrevInvisible($sort = false, $direction = 'asc') {
    return ($this->prevInvisible($sort, $direction)) ? true : false; 
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
   * Checks if this page object is the main site
   * 
   * @return false
   */
  public function isSite() {
    return false;
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
    return !$this->isVisible();
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
    return ($this->uri() === c::get('error')) ? true : false;    
  }

  /**
   * Compares this page with a given page object
   * 
   * @param object $page A Page object to compare
   * @return boolean
   */
  public function equals(Page $page) {
    return ($page === $this) ? true : false;
  }

  /**
   * Alternative for $this->equals()
   */
  public function is(Page $page) {
    return $this->equals($page);
  }

  /**
   * Checks if this page is active.
   * This means that the user is currently browsing this page.
   * 
   * @return boolean
   */
  public function isActive() {
    if(!is_null($this->isActive)) return $this->isActive;
    return $this->isActive = site::instance()->activePage()->equals($this); 
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

    // the active page is of course automatically open as well
    if($this->isActive()) return true;

    $u = array_values(site::instance()->uri()->path()->toArray());
    $p = str::split($this->uri(), '/');

    for($x = 0; $x < count($p); $x++) {
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
    
    $content = ($this->content()) ? $this->content()->data($key, null) : null;

    // try looking for an extension for that key
    if(is_null($content) && isset(static::$extensions[$key])) {
      if(is_callable(static::$extensions[$key])) {
        
        // add the page to the list of arguments
        if(is_array($arguments)) {
          $arguments = array_merge(array($this), $arguments);
        }

        return call_user_func_array(static::$extensions[$key], $arguments);  
      } else {
        return static::$extensions[$key];
      }
    } else if($content && $key == 'date') {
      $content = (!empty($arguments)) ? $content->toDate(a::first($arguments)) : $content->toTimestamp();
    }

    return $content;

  }

  /**
   * Setter for overwriting data
   * 
   * @param mixed $key
   * @param mixed $value
   */
  public function set($key, $value = '') {

    // check for content data for this page
    $content = $this->content();

    // create the content file if it doesn't exist yet
    if(!$content) $content = content::create($this);
    
    if(is_array($key)) {
      foreach($key as $k => $v) $content->set($k, $v);
      return true;
    } else {
      $content->set($key, $value);
    }

  }

  /**
   * Magic setter
   * 
   * @param mixed $key
   * @param mixed $value
   */  
  public function __set($key, $value) {
    $this->set($key, $value);
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
      'slug',
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
   * Returns the html for the current page
   * 
   * @return string
   */
  public function render() {

    $cache = new Cache($this);

    if($html = $cache->get()) {      
      // $html is already set
    } else {

      $site = site::instance();

      // share the site, pages, and page between all templates
      template::globals(array(
        'site'  => $site,
        'pages' => $site->children(),
        'page'  => $this
      ));

      // setup a new template for this page
      $template = new Template(KIRBY_SITE_ROOT_TEMPLATES . DS . $this->template());

      // render and cache the html
      $cache->set($html = $template->render());

    }

    return $html;

  }

  /**
   * Makes it possible to extend the page object 
   * For each extended key, there's a new magic getter available afterwards
   * 
   * @param string $key 
   * @param mixed $value Can be an object, a simple string or a callable function 
   */
  static public function extend($key, $value) {
    if(isset(static::$extensions[$key])) raise('The extension already exists: ' . $key);
    static::$extensions[$key] = $value;
  }

  /**
   * Creates a new page
   * 
   * @param string $uri The intended uri for the new page. The parent page must exist
   * @param array $data An array with all content fields, which should be stored for the new page
   * @param array $params An array with template name and num for the new page (optional)
   * @return mixed Returns false if the creation failed, otherwise returns the new page object
   */
  static public function create($uri, $data = array(), $params = array()) {

    $uri    = str::split($uri, '/');
    $slug   = str::slug(array_pop($uri));
    $uri    = implode('/', $uri);
    $parent = empty($uri) ? site::instance() : site::instance()->children()->find($uri);

    // check if the parent exists
    if(!$parent or empty($slug)) raise('The parent page could not be found', 'missing-parent');

    // check if the page already exists
    if($page = $parent->children()->find($slug)) return $page;

    $defaults = array(
      'template' => $slug,
      'num'      => null
    );

    $options = array_merge($defaults, $params);
    $root    = $parent->root() . DS . $slug;
    $file    = str::template('{root}' . DS . '{template}{lang}.{extension}', array(
      'root'      => $root,
      'template'  => $options['template'],
      'lang'      => r(site::$multilang, '.' . c::get('lang.default')),
      'extension' => c::get('content.file.extension', 'txt')
    ));

    // try to create the directory
    if(!\Kirby\Toolkit\dir::make($root)) raise('The directory could not be created', 'no-directory');

    // try to write the text file
    if(!\Kirby\Toolkit\Txtstore::write($file, $data)) raise('The data could not be saved', 'not-saved');

    // make sure the parent is up to date
    $parent->reset();

    // get the page object
    $page = $parent->children()->find($slug);

    // check if the new page object exists
    if(!$page) raise('The page object could not be found', 'missing-page');

    // finally sort the new page
    if($options['num']) $page->sort($options['num']);

    // get the final page object
    $page = $parent->children()->find($slug);
    
    if(!$page) raise('The page object could not be found', 'missing-page');

    // fetch the page's content so it's accessible right away
    $page->content();

    return $page;

  }

  /**
   * Moves a page to a different location
   */
  public function move($uri) {

    if($this->isSite() or $this->isHomePage() or $this->isErrorPage()) raise('You cannot move this page', 'unauthorized');

    $uri    = str::split($uri, '/');
    $slug   = str::slug(array_pop($uri));
    $uri    = implode('/', $uri);
    $parent = empty($uri) ? site::instance() : site::instance()->children()->find($uri);

    // check if the parent exists
    if(!$parent or empty($slug)) raise('The parent page does not exist', 'missing-parent');    

    // check if the page already exists
    if($page = $parent->children()->find($slug)) raise('The page already exists', 'page-exists');

    // create the new directory root
    $newRoot = $parent->root() . DS . r($this->num(), $this->num() . '-') . $slug;

    // try to move the entire directory
    if(!\Kirby\Toolkit\dir::move($this->root(), $newRoot)) raise('The page directory could not be moved', 'move-failed');

    // reset the parent to make sure the children are fetched correctly
    $parent->reset();

    // return the changed page object
    return $parent->children()->find($slug);

  }

  /**
   * Sorts the page with different modi: 
   * 
   * 1. any number
   * 2. first
   * 3. last
   * 4. up
   * 5. down
   * 
   * @param mixed $num
   * @return boolean
   */
  public function sort($num = null) {

    // don't sort the site object
    if($this->isSite()) raise('The site cannot be moved', 'is-site');

    // sanitize num
    if($num === 0) $num = '0';

    // get the current num if nothing is passed at all
    if(is_null($num)) $num = $this->num();

    // get all visible siblings without the current page
    $siblings = $this->siblings()->visible()->not($this);

    switch($num) {
      case 'up':
        if($this->isInvisible()) raise('The page is invisible', 'is-invisible');
        // sanitize the number and increase it
        return $this->sort((int)$this->num() + 1);
        break;
      case 'down':
        if($this->isInvisible()) raise('The page is invisible', 'is-invisible');
        // sanitize the number and decrease it
        return $this->sort((int)$this->num() - 1);
        break;
      case 'first':        
        return $this->sort(1);
        break;
      case 'last':
        return $this->sort($siblings->count() + 1);
        break;
      default:
        
        // sanitize the number
        $num = (int)$num;
        
        // don't allow higher numbers then the number of sortable items
        if($num > $siblings->count() + 1) $num = $siblings->count() + 1;

        // 0 will be replaced with one
        if($num <= 0) $num = 1;

        // change the numbers of all pages before the current page
        $n = 1;
        foreach($siblings->slice(0, $num - 1) as $p) {
          $p->num($n);
          $n++;
        }
        
        // change the number of the current page
        $this->num($num);
        
        // change the numbers of all pages after the current page
        $n = $num + 1;
        foreach($siblings->slice($num - 1) as $p) {
          $p->num($n);
          $n++;
        }

        return true;
        break;
    }

  }

  /**
   * Makes the page visible or invisible
   * 
   * @param string $status 'visible' or 'invisible'
   * @param mixed $num Optional sorting number
   * @return boolean
   */
  public function make($status, $num = 'last') {

    if($this->isSite()) raise('The site cannot be changed', 'is-site');

    switch($status) {
      case 'visible':
        $this->sort($num);
        break;
      case 'invisible':
        if($this->isInvisible()) return true;
        $dir = dirname($this->dir()) . DS . $this->uid();
        if(!\Kirby\Toolkit\dir::move($this->root(), $dir)) raise('The directory cannot be moved', 'move-failed');
        $this->root = $dir;
        $this->reset();
        break;
      default:
        raise('This method is not supported', 'unsupported-method');
    }

    return $this;

  }

  /**
   * Toggle the page's visibility
   *
   * @param mixed $num Optional num for $this->make('visible', $num) 
   * @return boolean
   */
  public function toggle($num = 'last') {
    return ($this->isVisible()) ? $this->make('invisible') : $this->make('visible', $num);
  }

  /**
   * Saves the page's content
   * 
   * @return boolean
   */
  public function save() {
    return $this->content()->save();
  }

  /** 
   * Deletes the page. 
   * Failes if the page has children, 
   * is the error or home page
   * 
   * @return boolean
   */
  public function delete() {
    
    // check if this is a deletable page
    if($this->isSite() or $this->isErrorPage() or $this->isHomePage()) {
      raise('You cannot delete this page', 'unauthorized');    
    }
    
    // check if this page has children 
    if($this->hasChildren()) {
      raise('This page has subpages. Please delete them first', 'has-children');
    }

    // try to remove the directory
    if(!\Kirby\Toolkit\dir::remove($this->root())) {
      raise('The directory could not be deleted', 'delete-failed');
    }

    $this->reset();
    return true;

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

  /**
   * Returns a more readable dump array for the dump() helper
   * 
   * @return array
   */
  public function __toDump() {

    $dump = array(
      'id'               => $this->id(),
      'url'              => $this->url(),
      'tinyurl'          => $this->tinyurl(),
      'uri'              => $this->uri(),
      'folder'           => $this->diruri(),
      'num'              => $this->num(),
      'active'           => $this->isActive(),
      'open'             => $this->isOpen(),
      'fields'           => ($this->content()) ? $this->content()->fields() : array(),
      'template'         => $this->template(),
      'intendedTemplate' => $this->intendedTemplate(),
      'parent'           => !$this->parent() or $this->parent()->isSite() ? '' : $this->parent()->uri(),
      'children'         => array(), 
      'files'            => array(), 
    );

    foreach($this->children() as $child) {
      $dump['children'][] = $child->diruri();
    }

    foreach($this->files() as $file) {
      $dump['files'][] = $file->uri();
    }

    return $dump;

  }

}