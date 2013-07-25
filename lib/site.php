<?php

namespace Kirby\CMS;

use Exception;
use Kirby\Toolkit\C;
use Kirby\Toolkit\Cache;
use Kirby\Toolkit\Dir;
use Kirby\Toolkit\Event;
use Kirby\Toolkit\F;
use Kirby\Toolkit\G;
use Kirby\Toolkit\Header;
use Kirby\Toolkit\Router;
use Kirby\Toolkit\Server;
use Kirby\Toolkit\URI;

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Site
 *
 * ATTENTION: use the site::instance() method or site() helper to get access to the site singleton
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

  // cache for the visitor object
  protected $visitor = null;

  // cache for the last modified date
  protected $modified = null;

  // cache for the current language 
  protected $language = array();

  // cache for all available languages
  protected $languages = null;

  // cache for added plugins
  protected $plugins = null;

  // returns true for multilanguage websites
  static public $multilang = false;

  // cache for the singelton instance
  static protected $instance = null;

  /**
   * Singleton accessor for the current site instance
   * 
   * @return object Site
   */
  static public function instance($params = array()) {
    if(is_null(static::$instance) or !empty($params)) {
      static::$instance = new Site($params);
    }
    return static::$instance;
  }

  /**
   * Constructor
   * 
   * @param array $params Additional options for the site. Can be used to overwrite config vars
   */
  public function __construct(array $params = array()) {

    // store this for the singleton
    static::$instance = $this;

    // load all needed config vars
    $this->configure($params);

    // initiate the page object with the given root    
    parent::__construct(KIRBY_CONTENT_ROOT);

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
   * @param string $uri This is only to comply with strict mode, because the page::uri method has a uri argument. It's not going to be used here. 
   * @return object uri
   */
  public function uri($uri = null) {

    // check for a cached uri object
    if(!is_null($this->uri)) return $this->uri;

    // subfolder setup
    $subfolder = $this->subfolder();

    // add the language code as subfolder 
    // for multi language websites
    if(static::$multilang) {
      if(c::get('lang.urls') != 'short' or !$this->language()->isDefault()) {
        $subfolder .= '/' . $this->language()->code();
      }
    }

    // init the uri object with the correct setup
    return $this->uri = new uri(array(
      // define the subfolder so we only get the relevant part of the path
      'subfolder' => $subfolder,
      // set a current URL if available in options
      'url' => c::get('currentURL', null)
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

    if(static::$multilang && $lang) {

      // return the specific language url
      return $this->language($lang)->url();

    } else {

      // look for a cached url
      if(!is_null($this->url)) return $this->url;

      // auto-detect the url if it is not set
      $url = (c::get('url') === false) ? $this->scheme() . '://' . $this->uri()->host() : rtrim(c::get('url'), '/');

      // handle subfolders
      if($subfolder = $this->subfolder()) {
        // check if the url already contains the subfolder      
        // so it's not included twice
        if(!preg_match('!' . preg_quote($subfolder) . '$!i', $url)) $url .= '/' . $subfolder;      
      }

      // if rewrite is deactivated
      // index.php needs to be prepended
      // so urls will still work
      if(!c::get('rewrite')) $url .= '/index.php';
      
      // store the final url in the config               
      c::set('url', $url);  
      
      // cache and return the final url
      return $this->url = $url;

    }

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
    if(empty($uri) or $uri == $this->subfolder()) $uri = c::get('home', 'home');

    // try to find an active page by the given uri
    $activePage = $this->children()->findByURI($uri, static::$multilang ? 'slug' : 'uid');

    // check if the active page is valid    
    if($activePage and $activePage->uri() == $uri) {      
      return $this->activePage = $activePage;    
    } else if($route = router::run($this->uri()->path())) {

      // get the route action
      $action = $route->action();

      // if the router action is a callable function…
      if($route->isCallable()) {
        // … call that function and pass all options from the url
        $result = $route->call();
      
        // if the router action returns a page, use that page
        // as the currently active page
        if(is_a($result, 'Kirby\\CMS\\Page')) {
          return $this->activePage = $result;
        } else {
          echo $result;
          exit();
        }

      // try to find a page for that uri
      } else if($p = $this->pages->find($action)) {
        return $this->activePage = $p;
      }

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

    // don't return anything if language support is switched off
    if(!site::$multilang) return null;

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

    // don't return anything if language support is switched off
    if(!site::$multilang) return null;

    if(is_null($code)) {

      if(isset($this->language['current'])) {
        return $this->language['current'];      
      } else {
        return $this->language['current'] = $this->languages()->findCurrent();  
      }
      
    } else if(isset($this->language[$code])) {
      return $this->language[$code];
    } else if($code == 'default') {
      return $this->language[$code] = $this->languages()->findDefault();
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
    if(!is_null($this->visitor)) return $this->visitor;
    return $this->visitor = new Visitor();
  }

  // rendering

  /**
   * Converts the current page to html
   * 
   * @return string
   */
  public function toHTML($echo = false) {

    $page = $this->activePage();
    $html = $page->toHtml();

    // send an 404 header for error pages
    if($page->isErrorPage() && c::get('error.header')) header::notfound();

    event::trigger('kirby.cms.site.toHTML', array($this, $page, &$html));

    if($echo) echo($html);
    return $html;

  }

  /**
   * Renders the page as HTML and echos the result
   */
  public function show() {
    event::trigger('kirby.cms.site.show', array($this));
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
   * @param string $template This argument has no effect and is just here to align to strict standards. 
   * @return string
   */
  public function template($template = null) {
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
    if(static::$multilang && $this->uri() == $this->subfolder()) {
      go($this->language()->url());
    }

    // try to resolve tiny urls if enabled and available
    if($url = tinyurl::resolve($this->uri()->toURL())) go($url);

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
    f::load(KIRBY_SITE_ROOT_CONFIG . DS . 'config.php');
    f::load(KIRBY_SITE_ROOT_CONFIG . DS . 'config.' . server::get('server_addr') . '.php');
    f::load(KIRBY_SITE_ROOT_CONFIG . DS . 'config.' . server::get('server_name') . '.php');

    // merge the late options
    c::set($params);

    // connect the cache 
    try {
      cache::connect('file', array('root' => KIRBY_SITE_ROOT_CACHE));
    } catch(Exception $e) {
      // do nothing. Caching will just fail silently
    }

    // check for multilang support
    static::$multilang = c::get('lang.support');

    // store all important roots in the config array
    c::set(array(
      'root'           => KIRBY_INDEX_ROOT,
      'root.content'   => KIRBY_CONTENT_ROOT,
      'root.kirby'     => KIRBY_CMS_ROOT,
      'root.site'      => KIRBY_SITE_ROOT, 
      'root.templates' => KIRBY_SITE_ROOT_TEMPLATES,
      'root.snippets'  => KIRBY_SITE_ROOT_SNIPPETS,      
      'root.plugins'   => KIRBY_SITE_ROOT_PLUGINS,      
      'root.cache'     => KIRBY_SITE_ROOT_CACHE,      
    ));

  }

  /**
   * Initializes some basic local settings
   */  
  protected function localize() {

    // set the timezone to make sure we 
    // avoid errors in php 5.3
    @date_default_timezone_set(c::get('timezone'));

    if(site::$multilang and $this->language()->locale()) {
      // set the local for the specific language
      setlocale(LC_ALL, $this->language()->locale());      
    } else if(c::get('locale')) {
      // set default locale settings for php functions
      setlocale(LC_ALL, c::get('locale'));      
    }

    // load all language vars
    f::load(KIRBY_SITE_ROOT_LANGUAGES . DS . c::get('lang.default') . '.php');
    f::load(KIRBY_SITE_ROOT_LANGUAGES . DS . c::get('lang.current') . '.php');

  } 

  /**
   * Internal system health checks
   */
  protected function health() {

    // check for a readable content directory
    if(!is_dir($this->root)) raise('The content directory is not readable');

    // check for an existing site directory
    if(!is_dir(KIRBY_SITE_ROOT)) raise('The site directory is not readable');

    // check for a proper phpversion
    if(floatval(phpversion()) < 5.3) raise('Please upgrade to PHP 5.3 or higher');

    // check for existing mbstring functions
    if(!function_exists('mb_strtolower')) raise('mb_string functions are required in order to run Kirby properly');
    
  }

  /**
   * Returns a more readable dump array for the dump() helper
   * 
   * @return array
   */
  public function __toDump() {

    $dump = array_merge(parent::__toDump(), array(
      'uri'       => $this->uri()->__toDump(),
      'languages' => $this->languages()->__toDump(),
      'plugins'   => $this->plugins()->__toDump(),
    ));

    unset($dump['id']);
    unset($dump['folder']);
    unset($dump['num']);
    unset($dump['active']);
    unset($dump['open']);
    unset($dump['template']);
    unset($dump['intendedTemplate']);
    unset($dump['parent']);

    return $dump;
  
  }

}