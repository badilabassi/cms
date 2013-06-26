<?php

namespace Kirby\CMS;

use Kirby\Toolkit\C;
use Kirby\Toolkit\Cache;
use Kirby\Toolkit\Dir;
use Kirby\Toolkit\F;
use Kirby\Toolkit\G;
use Kirby\Toolkit\Router;
use Kirby\Toolkit\Server;
use Kirby\Toolkit\URI;

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Site
 *
 * ATTENTION: use the site() helper function to get access to the site singleton
 * 
 * This is the main site object
 * Everything else is dependent on this
 * The site object is also used with the site() 
 * singleton function to initiate a Kirby site.
 * 
 * @package   Kirby CMS
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://getkirby.com/license
 */
class Site extends Page {

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

  // cache for the current language 
  protected $language = array();

  // cache for all available languages
  protected $languages = null;

  // cache for added plugins
  protected $plugins = null;

  /**
   * Constructor
   * 
   * @param array $params Additional options for the site. Can be used to overwrite config vars
   */
  public function __construct(array $params = array()) {

    g::set('site', $this);

    // load all needed config vars
    $this->configure($params);

    // initiate the page object with the given root    
    parent::__construct(KIRBY_CONTENT_ROOT);

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
    $this->localize();

    // load all available parsers
    $this->parsers();

    // initialize plugins
    $this->plugins();

    // get the main url for the site
    $this->url();

    // check for system health
    $this->health();

    // show the troubleshoot modal if required
    $this->troubleshoot();

  }

  /**
   * Displays the troubleshooting site and 
   * stops code execution
   */
  public function troubleshoot() {
    
    if(!c::get('troubleshoot')) return false;

    require(KIRBY_CMS_ROOT_MODALS . DS . 'troubleshoot.php');
    exit();
  
  }

  /**
   * Returns the uri object, which can be used
   * to inspect and work with the current URL/URI
   * 
   * @return object uri
   */
  public function uri($uri = null) {

    if(!is_null($this->uri)) return $this->uri;

    return $this->uri = new uri(array(
      // attach the language code to the subfolder if multi-lang support is activated
      'subfolder' => (!c::get('lang.support')) ? $this->subfolder() : $this->subfolder() . '/' . $this->language()->code(),
      // set a current URL if available in options
      'url' => c::get('currentURL', null)
    ));

  }

  /**
   * The site doesn't have a translated uid
   * 
   * @return null
   */
  public function translatedUID() {
    return null;
  }

  /**
   * The site doesn't have a translated uri
   * 
   * @return null
   */
  public function translatedURI() {
    return null;
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
    return $this->modified = (c::get('cache.autoupdate') && c::get('cache')) ? dir::modified($this->root()) : 0;                  
  }

  /**
   * Returns the first set of pages/children in the content directory (the main pages of your site)
   * You can also use $site->children() to get the same collection
   * 
   * @return object Pages
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
   * @return object Page
   */
  public function homePage() {
    if(!is_null($this->homePage)) return $this->homePage;
    $this->homePage = $this->children()->findBy('uid', c::get('home', 'home'), false);
    // if the home page can't be found something is truly wrong
    if(!$this->homePage) raise('The home page could not be found');
    return $this->homePage;
  }

  /**
   * Returns the error page of the site
   * 
   * @return object Page
   */
  public function errorPage() {
    if(!is_null($this->errorPage)) return $this->errorPage;
    $this->errorPage = $this->children()->findBy('uid', c::get('error', 'error'), false);
    // if the error page can't be found something is truly wrong
    if(!$this->errorPage) raise('The error page could not be found');
    return $this->errorPage;
  }

  /**
   * Returns the currently active page of the site
   * 
   * @return object Page
   */
  public function activePage() {

    // try to get the active page from cache
    if(!is_null($this->activePage)) return $this->activePage;

    // get the current uri path
    $uri = (string)$this->uri()->path();

    // if the path is empty, use the homepage uid
    if(empty($uri) || $uri == $this->subfolder()) $uri = c::get('home', 'home');

    // try to find an active page by the given uri
    $activePage = $this->children()->find($uri);

    // check if the active page is valid    
    if($activePage && $activePage->translatedURI() == $uri) {      
      return $this->activePage = $activePage;    
    } else if($route = router::match($this->uri())) {

      // path to an existing page
      $uri = $route->action();      

      // try to find a page for that uri
      if($p = $this->pages->find($uri)) return $this->activePage = $p;

    } 

    // fallback to the error page
    return $this->activePage = $this->errorPage();

  }
  
  /**
   * Returns all available languages for this site
   * 
   * @return object Languages
   */
  public function languages() {
    if(!is_null($this->languages)) return $this->languages;
    return $this->languages = new Languages();
  }

  /**
   * Returns the current language or any other language
   * if a language code is passed as argument
   * 
   * @param string $code An optional language code to return any available language
   * @return object Language
   */
  public function language($code = null) {

    if(is_null($code)) {

      if(isset($this->language['current'])) {
        return $this->language['current'];      
      } else {
        return $this->language['current'] = $this->languages()->findActive();  
      }
      
    } else if(isset($this->language[$code])) {
      return $this->language[$code];
    } else {
      return $this->language[$code] = $this->languages()->find($code);
    }

  }

  /**
   * Load available parsers
   */
  protected function parsers() {
    require_once(KIRBY_CMS_ROOT_PARSERS . DS . 'smartypants.php');
    require_once(KIRBY_CMS_ROOT_PARSERS . DS . r(c::get('markdown.extra'), 'markdown.extra.php', 'markdown.php'));
  }

  /**
   * Returns the Plugins object with all installed plugins
   * 
   * @return object Plugins
   */
  public function plugins() {
    if(!is_null($this->plugins)) return $this->plugins;
    
    $this->plugins = new Plugins();
    $this->plugins->load();

    return $this->plugins;
  
  }

  /**
   * Shortcut for the visitor plugin instance
   * 
   * @return object Visitor
   */
  public function visitor() {
    return $this->plugins()->visitor()->instance();
  }

  /**
   * Returns a collection of pages, which are currently open
   * This is perfect to create a breadcrumb navigation
   * 
   * @return object Pages
   */
  public function breadcrumb() {
    return $this->plugins()->breadcrumb()->instance();
  }

  // rendering

  /**
   * Converts the current page to html
   * 
   * @return string
   */
  public function toHtml($echo = false) {

    $this->html = $this->activePage()->toHtml();

    if($echo) echo($this->html);
    return $this->html;

  }

  /**
   * Renders the page as HTML and echos the result
   */
  public function show() {
    echo $this->toHtml();
  }

  /**
   * Checks if this page object is the main site
   * 
   * @return true
   */
  public function isSite() {
    return true;
  }

  /**
   * Returns the intended template
   * 
   * @return string
   */
  public function intendedTemplate() {
    return 'site';
  }

  /**
   * Returns the usable template
   * 
   * @return string
   */
  public function template() {
    return 'site';    
  }

  /**
   * The site object has a depth of 0
   * 
   * @return int 0
   */
  public function depth() {
    return 0;
  }

  /**
   * Handles URL rewriting in particular situations 
   * like forcing https or removing/adding index.php 
   */  
  public function rewrite() {

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

    // in case the url has no language code yet redirect 
    // to the default language home page i.e. from / to /en
    if(c::get('lang.support') && $this->uri() == $this->subfolder()) {
      go($this->language()->url());
    }

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

  /**
   * Get variables and lazy loaded attributes
   */
  public function __get($key) {
    if(method_exists($this, $key)) return $this->$key();
    return ($this->content()) ? $this->content()->$key() : null;
  }

  // protected methods

  /**
   * Loads all config files 
   */
  protected function configure($params = array()) {

    // load custom config files
    f::load(KIRBY_PROJECT_ROOT_CONFIG . DS . 'config.php');
    f::load(KIRBY_PROJECT_ROOT_CONFIG . DS . 'config.' . server::get('server_addr') . '.php');
    f::load(KIRBY_PROJECT_ROOT_CONFIG . DS . 'config.' . server::get('server_name') . '.php');

    // get all config options that have been stored so far
    $defaults = c::get();

    // merge them with the passed late options again
    $config = array_merge($defaults, $params);

    // store them again
    c::set($config);

    // connect the cache 
    if(c::get('cache')) cache::connect('file', array('root' => KIRBY_PROJECT_ROOT_CACHE));

  }

  /**
   * Initializes some basic local settings
   */  
  protected function localize() {

    // set the timezone to make sure we 
    // avoid errors in php 5.3
    @date_default_timezone_set(c::get('timezone'));

    // set default locale settings for php functions
    if(c::get('lang.locale')) setlocale(LC_ALL, c::get('lang.locale'));

    // store the current language code in the config
    if(c::get('lang.support')) c::set('lang.current', $this->language()->code());

    // load all language vars
    f::load(KIRBY_PROJECT_ROOT_LANGUAGES . DS . c::get('lang.default') . '.php');
    f::load(KIRBY_PROJECT_ROOT_LANGUAGES . DS . c::get('lang.current') . '.php');

  } 

  /**
   * Internal system health checks
   */
  protected function health() {

    // check for a readable content directory
    if(!is_dir($this->root)) raise('The content directory is not readable');

    // check for an existing site directory
    if(!is_dir(KIRBY_PROJECT_ROOT)) raise('The site directory is not readable');

    // check for a proper phpversion
    if(floatval(phpversion()) < 5.2) raise('Please upgrade to PHP 5.2 or higher');

    // check for existing mbstring functions
    if(!function_exists('mb_strtolower')) raise('mb_string functions are required in order to run Kirby properly');
    
  }

}