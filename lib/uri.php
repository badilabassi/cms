<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

// dependencies
require(ROOT_KIRBY_LIB . DS . 'uri' . DS . 'params.php');
require(ROOT_KIRBY_LIB . DS . 'uri' . DS . 'path.php');
require(ROOT_KIRBY_LIB . DS . 'uri' . DS . 'query.php');

/**
 * URI
 *
 * The Kirby URI object makes it possible
 * to inspect the current URL and modify it
 * 
 * @package Kirby CMS
 */
class KirbyUri {

  // all options 
  protected $options = array();
  
  // the file in the URL (i.e. index.php)
  protected $file;
    
  // the subfolder if defined in options
  protected $subfolder;
  
  // the path object (KirbyUriPath)
  protected $path;
  
  // the params object (KirbyUriParams)
  protected $params;
  
  // the query object (KirbyUriQuery)
  protected $query;

  // the file extension (i.e. html, js, php, etc)
  protected $extension;

  // the original URL
  protected $original;

  // the scheme (http or https)
  protected $scheme;
  
  // the hostname
  protected $host;
  
  // the base url http://yourdomain.com
  protected $baseurl;
  
  // the full url
  protected $url;
  
  // the parsed results
  protected $parsed;

  /**
   * Constructor
   * 
   * @param string $url
   * @param array $options
   */
  public function __construct($url = null, $options = array()) {

    if(is_array($url)) {
      $options = $url;
      $url     = a::get($options, 'url', null);
    }

    $this->set($url, $options);

  }

  /**
   * Sets the inspectable url and additional options
   * 
   * @param string $url
   * @param array $options
   */
  public function set($url = null, $options = array()) {

    $defaults = array(
      'subfolder' => null,
    );

    // reset all attributes
    $this->options   = array_merge($defaults, $options);
    $this->file      = null;
    $this->subfolder = null;
    $this->path      = null;
    $this->params    = null;
    $this->query     = null;
    $this->extension = null;
    $this->original  = null;
    $this->scheme    = null;
    $this->host      = null;
    $this->baseurl   = null;
    $this->url       = null;
    $this->parsed    = null;

    // parse the given url;
    $this->parse($url);
        
  }

  /**
   * Private parsing method which will take the URL 
   * apart and analyze all its components
   * 
   * @param string $url
   */
  private function parse($url) {
    
    $defaults = array(
      'scheme' => (isset($_SERVER['HTTPS'])) ? 'https' : 'http',
      'host'   => server::get('http_host'),
      'path'   => '',
      'port'   => '',
      'query'  => false,
    );
    
    // build the full url if not given
    if(!$url) {
      $url = $defaults['scheme'] . '://' . $defaults['host'] . server::get('request_uri');
    }

    $this->original = $url;                    
    $this->parsed   = (object)array_merge($defaults, (array)parse_url($url));
            
    // take some values from the parsed object and store it in URI attributes
    $this->scheme = $this->parsed->scheme;
    $this->host   = $this->parsed->host;
            
    // parse the query string into an array
    @parse_str($this->parsed->query, $this->parsed->query);
    
    // add the subfolder from the options array
    $this->subfolder = $this->options['subfolder'];

    // detect the file
    $this->file = $this->file = basename($_SERVER['SCRIPT_NAME']);
    
    // get the path    
    $path = $this->parsed->path;
    
    // strip the filename from the path
    if($this->file) $path = trim(str_replace($this->file, '', $path), '/');

    // strip subfolders from uri    
    if($this->subfolder) {
        
      // subfolder autodetection
      if($this->subfolder == '__autodetect') $this->subfolder = trim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
      
      // make sure to clean the passed subfolder to avoid errors
      $path = trim(preg_replace('!^' . preg_quote($this->subfolder) . '(\/|)!i', '/', $path), '/');

    }
    
    // split the path
    $parts = (array)str::split($path, '/');

    // use ; as parameter separator on win
    $sep = DS == '/' ? ':' : ';';

    // start a new params array
    $params = array();
    $path   = array();
    $pindex = 1;

    // parse params
    foreach($parts AS $p) {
      if(preg_match('/\\'. $sep . '/', $p)) {
        $parts = explode($sep, $p);
        if(count($parts) < 2) continue;
        $params[$parts[0]] = urldecode($parts[1]);
      } else {
        $path[$pindex] = urldecode($p);
        $pindex++;
      }
    }

    // get the extension from the last part of the path    
    $this->extension = f::extension(a::last($path));

    // handle the extension on the last element    
    if($this->extension) {
      // remove the last part of the path
      $last = array_pop($path);
      // add it again to the path array without extension
      $path[] = f::name($last);

      $this->file = $last;

    } else {
      $this->extension = f::extension($this->file);
    }    

    // create a new params and path array    
    $this->params = new KirbyUriParams($params);
    $this->path   = new KirbyUriPath($path);
    $this->query  = new KirbyUriQuery($this->parsed->query);
                
    return $this->path;
                
  }

  /**
   * Returns the scheme of the URL (http or https)
   * 
   * @return string
   */
  public function scheme() {
    return $this->scheme;
  }

  /**
   * Returns the host name 
   * 
   * @return string
   */
  public function host() {
    return $this->host;
  }

  /**
   * Returns the original url
   * 
   * @return string
   */
  public function original() {
    return $this->original;
  }

  /**
   * Returns the base url (ie. http://yourdomain.com)
   * 
   * @return string
   */
  public function baseurl() {

    if(!is_null($this->baseurl)) return $this->baseurl;

    // check for a port
    $port = ($this->parsed->port) ? ':' . $this->parsed->port : '';

    // create a full url out of this
    $parts   = array();
    $parts[] = $this->scheme . '://' . $this->host . $port;
    $parts[] = $this->subfolder;

    return $this->baseurl = rtrim(implode('/', $parts), '/');

  }

  /**
   * Returns the subfolder if defined in $this->options
   * 
   * @return string
   */
  public function subfolder() {
    return $this->subfolder;
  }

  /**
   * Returns the file (i.e. test.php)
   * 
   * @return string
   */
  public function file() {
    return $this->file;
  }

  /**
   * Returns the file extension (i.e. html, js, php, etc)
   * 
   * @return string
   */
  public function extension() {
    return $this->extension;
  }

  /**
   * Returns the full url
   * 
   * @return string
   */
  public function url() {
    return $this->toURL();
  }
  
  /**
   * Smart getter for the KirbyPath object
   * 
   * @param mixed $key null: get the entire path object, int: get a part of the path 
   * @param mixed $default null: no default. string: a default value if nothing is found for this key
   * @return string
   */
  public function path($key=null, $default=null) {
    if(is_null($key)) return $this->path;
    return $this->path->get($key, $default);
  }

  /**
   * Smart getter for the KirbyParams object
   * 
   * @param mixed $key null: get the entire params object, string: get a part of the params object by key 
   * @param mixed $default null: no default. string: a default value if nothing is found for this key
   * @return string
   */
  public function param($key=null, $default=null) {
    if(is_null($key)) return $this->params;
    return $this->params->get($key, $default);
  }

  /**
   * Alternative for $this->param()
   */
  public function params($key=null, $default=null) {
    return $this->param($key, $default);
  }
    
  /**
   * Smart getter for the KirbyQuery object
   * 
   * @param mixed $key null: get the entire query object, string: get a part of the query object by key 
   * @param mixed $default null: no default. string: a default value if nothing is found for this key
   * @return string
   */
  public function query($key=null, $default=null) {
    if(is_null($key)) return $this->query;
    return $this->query->get($key, $default);
  }

  /**
   * Takes care of properly cloning child objects 
   * 
   * @return object $this
   */
  public function __clone() {
    $this->path   = clone $this->path;
    $this->params = clone $this->params;
    $this->query  = clone $this->query;
    return $this;
  }

  /**
   * Makes it possible to echo the object
   * and get a string represantation of the URI
   * 
   * @return string
   */
  public function __toString() {
    return $this->toString();
  }

  /**
   * Converts the URI object to a string
   * This returns the path and optionally the query
   *
   * @param boolean $includeQuery true: the query will be appended, false: this will only return the path 
   * @return string
   */
  public function toString($includeQuery = true) {
    
    $parts  = array();
    $path   = $this->path();
    $params = $this->params();
    $query  = $this->query();
        
    if($path->get())   $parts[] = (string)$path;
    if($params->get()) $parts[] = (string)$params;

    if($includeQuery && $query->get()) $parts[] = '?' . $query;
        
    return implode('/', $parts);  
  
  }

  /**
   * Converts the URI object to a proper URL
   * 
   * @param boolean $includeQuery true: the query will be appended, false: this will only return the path 
   * @return string
   */
  public function toUrl($includeQuery=true) {
    
    $parts = array();
    $parts[] = $this->baseurl();
    $parts[] = $this->toString($includeQuery);

    return rtrim(implode('/', $parts), '/');
  
  }
  
  /**
   * Converts the URI object to a unique md5 hash
   * 
   * @return string
   */
  public function toHash() {
    $url = $this->toURL();
    return md5($url);    
  }

  /**
   * Removes the entire path 
   * 
   * @return $this
   */
  public function stripPath() {
    $this->path = new KirbyUriPath();
    return $this;
  }

  /**
   * Replaces a parameter with a new value
   * 
   * @param string $key
   * @param mixed $value
   * @return object $this
   */
  public function replaceParam($key, $value) {
    $this->params->replace($key, $value);
    return $this;
  }

  /**
   * Removes a parameter
   * 
   * @param string $key
   * @return object $this
   */
  public function removeParam($key) {
    $this->params->remove($key);
    return $this;
  }

  /**
   * Removes all parameters 
   * 
   * @return $this
   */
  public function stripParams() {
    $this->params = new KirbyUriParams();
    return $this;
  }

  /**
   * Removes a parameter and returns the URL
   * 
   * @param string $param
   * @return string 
   */
  public function urlWithoutParam($param) {
    $this->removeParam($param);
    return $this->toUrl();    
  }

  /**
   * Replaces a query key with a new value
   * 
   * @param string $key
   * @param mixed $value
   * @return object $this
   */
  public function replaceQueryKey($key, $value) {
    $this->query->replace($key, $value);
    return $this;
  }

  /**
   * Removes a query key
   * 
   * @param string $key
   * @return object $this
   */
  public function removeQueryKey($key) {
    $this->query->remove($key);
    return $this;
  }

  /**
   * Removes the entire query
   * 
   * @return object $this
   */
  public function stripQuery() {
    $this->query = new KirbyUriQuery();
    return $this;
  }

  /**
   * Removes a query key first and then returns the url
   * 
   * @return object $this
   */
  public function urlWithoutQueryKey($key) {
    $this->removeQueryKey($key);
    return $this->toUrl();    
  }

} 