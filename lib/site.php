<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

// dependencies
require_once('object.php');
require_once('page.php');
require_once('uri.php');
require_once('helpers.php');
require_once('template.php');
require_once('loader.php');
require_once('cache' . DS . 'html.php');
require_once('language.php');
require_once('languages.php');
require_once('visitor.php');
require_once('legacy.php');

/** 
 * Singleton handler for the site object
 * Always use this to initiate the site!
 * 
 * You can reinitiate the site by passing 
 * the $params argument. 
 * 
 * @param array $params Additional params to overwrite/set config vars
 * @return object KirbySite
 */
function site($params = array()) {
  static $site;
  if(!$site || !empty($params)) $site = new KirbySite($params);
  return $site;
}

/**
 * Site
 *
 * This is the main site object
 * Everything else is dependent on this
 * The site object is also used with the site() 
 * singleton function to initiate a Kirby site.
 * 
 * @package Kirby CMS
 */
class KirbySite extends KirbyPage {

  // cache for the uri object
  protected $uri = null;

  // the current scheme (http or https)
  protected $scheme = null;

  // the main url of this site
  protected $url = null;

  // the detected/set subfolder
  protected $subfolder = null;

  // cache for the home page object
  protected $homePage = null;

  // cache for the error page object
  protected $errorPage = null;

  // cache for the active page object
  protected $activePage = null;

  // cache for the breadcrumb collection
  protected $breadcrumb = null;

  // cache for the last modified date
  protected $modified = null;

  // cache for the generated html
  protected $html = null;

  // cache for the loader object
  protected $loader = null;

  // cache for the current language 
  protected $language = array();

  // cache for all available languages
  protected $languages = null;

  // cache for the visitor object
  protected $visitor = null;

  /**
   * Constructor
   * 
   * @param array $params Additional options for the site. Can be used to overwrite config vars
   */
  public function __construct(array $params = array()) {

    // load the site specific config files
    $this->load()->config($params);

    if(c::get('debug')) {
      // switch on all errors
      error_reporting(E_ALL);
      ini_set('display_errors', 1);
    } else {
      // switch off all errors
      error_reporting(0);
      ini_set('display_errors', 0);
    }

    // apply all locale settings and timezone stuff
    $this->localization();

    // load additional stuff
    $this->load()->parsers();
    $this->load()->plugins();
    $this->load()->language();
    
    // initiate the page object with the given root    
    parent::__construct(c::get('root.content'));

    // get the main url for the site
    $this->url();

    // check for system health
    $this->health();

    // show the troubleshoot modal if required
    $this->troubleshoot();

    // router 
    $this->router();

  }

  /**
   * Initiates the KirbyLoader object, which 
   * handles loading of additional dependencies and plugins
   * @return object KirbyLoader
   */
  public function load() {
    if(!is_null($this->loader)) return $this->loader;
    return $this->loader = new KirbyLoader();
  }

  /**
   * Displays the troubleshooting site and 
   * stops code execution
   */
  public function troubleshoot() {
    
    if(!c::get('troubleshoot')) return false;

    require_once(c::get('root.modals') . DS . 'troubleshoot.php');
    exit();
  
  }

  /**
   * Returns the KirbyURI object, which can be used
   * to inspect and work with the current URL/URI
   * 
   * @return object KirbyUri
   */
  public function uri($uri = null) {

    if(!is_null($this->uri)) return $this->uri;

    return $this->uri = new KirbyUri(array(
      'subfolder' => $this->subfolder(),
      'url'       => c::get('currentURL', null)
    ));

  }

  /**
   * Returns the subfolder(s)
   * A subfolder will be auto-detected or can be set in the config file
   * If you run the site under its own domain, the subfolder will be empty
   * 
   * @return string
   */
  public function subfolder() {

    if(!is_null($this->subfolder)) return $this->subfolder;

    // try to detect the subfolder      
    $subfolder = (c::get('subfolder') !== false) ? trim(c::get('subfolder'), '/') : trim(dirname(server::get('script_name')), '/\\');
    
    c::set('subfolder', $subfolder);
    return $this->subfolder = $subfolder;

  }

  /**
   * Returns the scheme (http or https)
   *
   * @return string
   */
  public function scheme() {
    if(!is_null($this->scheme)) return $this->scheme;
    return $this->uri()->scheme();
  }

  /**
   * Checks if the current page is visited with an encrypted connection
   * 
   * @return boolean
   */
  public function ssl() {
    return ($this->scheme() == 'https') ? true : false;
  }

  /**
   * Returns the base url of the site
   * The url is auto-detected by default and can 
   * also be set in the config like the subfolder
   * 
   * @return string
   */
  public function url($lang = false) {

    if(is_null($this->url)) {

      // auto-detect the url if it is not set
      $url = (c::get('url') === false) ? $this->scheme() . '://' . $this->uri()->host() : rtrim(c::get('url'), '/');

      if($subfolder = $this->subfolder()) {
        // check if the url already contains the subfolder      
        // so it's not included twice
        if(!preg_match('!' . preg_quote($subfolder) . '$!i', $url)) $url .= '/' . $subfolder;      
      }
                    
      c::set('url', $url);  
      $this->url = $url;

    }

    if(c::get('lang.support') && $lang) {
      return $this->language($lang)->url();
    }

    return $this->url;

  }

  /**
   * Returns the last modified date (unix timestamp)
   * This will go through all subfolders of the content directory
   * and check for modifications unless you set cache.autoupdate to false. 
   * 
   * @return int
   */
  public function modified() {
    if(!is_null($this->modified)) return $this->modified;
    return $this->modified = (c::get('cache.autoupdate')) ? dir::modified($this->root()) : 0;                  
  }

  /**
   * Returns the first set of pages/children in the content directory (the main pages of your site)
   * You can also use $site->children() to get the same collection
   * 
   * @return object KirbyPages
   */
  public function pages() {
    return $this->children();
  }

  /**
   * Creates a full indexed array with all pages, subpages, subsubpages, etc. of the site
   * This is perfect to search for something on all pages. It's therefor used by the search plugin
   * 
   * @return array
   */
  public function index() {
    return $this->children()->index();
  }

  /**
   * Returns the home page of the site
   * 
   * @return object KirbyPage
   */
  public function homePage() {
    if(!is_null($this->homePage)) return $this->homePage;
    return $this->homePage = $this->children()->find(c::get('home', 'home'));
  }

  /**
   * Returns the error page of the site
   * 
   * @return object KirbyPage
   */
  public function errorPage() {
    if(!is_null($this->errorPage)) return $this->errorPage;
    return $this->errorPage = $this->children()->find(c::get('error', 'error'));
  }

  /**
   * Returns the currently active page of the site
   * 
   * @return object KirbyPage
   */
  public function activePage() {

    // try to get the active page from cache
    if(!is_null($this->activePage)) return $this->activePage;

    // get the current uri path
    $uri = (string)$this->uri()->path();
    
    // if the path is empty, use the homepage uid
    if(empty($uri)) $uri = c::get('home', 'home');

    // try to find an active page by the given uri
    $activePage = $this->children()->find($uri);

    if($activePage) {
      $pageUri = (c::get('lang.support')) ? $activePage->translatedURI() : $activePage->uri();
    } else {
      $pageUri = c::get('error', 'error');
    }
    
    if(!$activePage || $pageUri != $uri) {
      $activePage = $this->errorPage();
    }
               
    return $this->activePage = $activePage;

  }

  /**
   * Returns a collection of pages, which are currently open
   * This is perfect to create a breadcrumb navigation
   * 
   * @return object KirbyPages
   */
  public function breadcrumb() {

    if(!is_null($this->breadcrumb)) return $this->breadcrumb;

    $path  = $this->uri()->path()->toArray(); 
    $crumb = array();
  
    foreach($path AS $p) {
      $tmp  = implode('/', $path);
      $data = $this->pages()->find($tmp);
            
      if(!$data || $data->isErrorPage()) {
        // add the error page to the crumb
        $crumb[] = $this->errorPage();
        // don't move on with subpages, because there won't be 
        // any if the first page hasn't been found at all
        break;
      } else {      
        $crumb[] = $data;
      }
      array_pop($path);        
    }
    
    // we've been moving through the uri from tail to head
    // so we need to reverse the array to get a proper crumb    
    $crumb = array_reverse($crumb);   

    // add the homepage to the beginning of the crumb array
    array_unshift($crumb, $this->homePage());
    
    // make it a pages object so we can handle it
    // like we handle all pages on the site  
    return $this->breadcrumb = new KirbyPages($crumb);

  }
  
  /**
   * Checks if this page has a specific plugin
   * Pass the name of the plugin to check for
   * 
   * @param string $plugin The name of the plugin
   * @return boolean
   */
  public function hasPlugin($plugin) {
    return (file_exists(c::get('root.plugins') . DS . $plugin . '.php') || file_exists(c::get('root.plugins') . DS . $plugin . DS . $plugin . '.php')) ? true : false;      
  }

  /**
   * Returns the visitor object 
   * 
   * @return object KirbyVisitor
   */
  public function visitor() {
    if(!is_null($this->visitor)) return $this->visitor;
    return $this->visitor = new KirbyVisitor();
  }

  /**
   * Returns all available languages for this site
   * 
   * @return object KirbyLanguages
   */
  public function languages() {
    if(!is_null($this->languages)) return $this->languages;
    return $this->languages = new KirbyLanguages();
  }

  /**
   * Returns the current language or any other language
   * if a language code is passed as argument
   * 
   * @param string $code An optional language code to return any available language
   * @return object KirbyLanguage
   */
  public function language($code = null) {
    if(is_null($code)) $code = c::get('lang.default');
    if(isset($this->language[$code])) return $this->language[$code];
    return $this->language[$code] = $this->languages()->find($code);
  }

  // rendering

  /**
   * Converts the current page to html
   * 
   * @return string
   */
  public function html() {

    $page  = $this->activePage();
    $cache = new KirbyHTMLCache($this, $page);

    if($data = $cache->get()) {
      return $this->html = $data;
    } else {

      if(!is_null($this->html)) return $this->html;

      tpl::set('site',  $this);
      tpl::set('pages', $this->children());
      tpl::set('page',  $page);

      $this->html = tpl::load($page->template(), false, true);

      $cache->set($this->html);
      return $this->html;

    }

  }

  /**
   * The site object has a depth of 0
   * 
   * @return int 0
   */
  public function depth() {
    return 0;
  }

  // magic stuff

  /**
   * Creates a link to the main url of this site. 
   * It's just there for debugging and to avoid errors when 
   * someone tries to echo the entire site object
   * 
   * @return string
   */
  public function __toString() {
    return '<a href="' . $this->url() . '">' . $this->url() . '</a>';
  }

  // protected methods

  /**
   * Initializes some basic local settings
   */  
  protected function localization() {

    // set the timezone to make sure we 
    // avoid errors in php 5.3
    @date_default_timezone_set(c::get('timezone'));

    // set default locale settings for php functions
    if(c::get('lang.locale')) setlocale(LC_ALL, c::get('lang.locale'));

  } 

  /**
   * Handles URL rewriting in particular situations 
   * like forcing https or removing/adding index.php 
   */  
  protected function router() {

    $page = $this->activePage();

    // check for ssl
    if(c::get('ssl')) {
      // if there's no https in the url
      if(!server::get('https')) go(str_replace('http://', 'https://', $page->url()));
    }

    // check for index.php in rewritten urls and rewrite them
    if(c::get('rewrite') && preg_match('!index.php!i', $this->uri()->original())) {      
      go($page->url());    
    }
  
  }

  /**
   * Internal system health checks
   */
  protected function health() {

    // check for a readable content directory
    if(!is_dir($this->root)) raise('The content directory is not readable');

    // check for an existing site directory
    if(!is_dir(c::get('root.site'))) raise('The site directory is not readable');

    // check for a proper phpversion
    if(floatval(phpversion()) < 5.2) raise('Please upgrade to PHP 5.2 or higher');

    // check for existing mbstring functions
    if(!function_exists('mb_strtolower')) raise('mb_string functions are required in order to run Kirby properly');
    
  }

}