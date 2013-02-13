<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

// dependencies
require_once(LIB . DS . 'html' . DS . 'html.php');
require_once(LIB . DS . 'html' . DS . 'form.php');
require_once(LIB . DS . 'html' . DS . 'embed.php');

/**
 * Main URL builder
 * 
 * Use this in all your templates to make
 * sure you get proper URls. 
 * 
 * @param string $uri relative url: will be resolved to an absolute url, absolute url: will be kept, false: will return the base url for this site
 * @param string $lang Add a language key to get a specific url for this language
 * @param array An optional associative array to build a set of params
 * @param array An option associative array to build a query string
 * @return string 
 */
function url($uri = false, $lang = false, $params = array(), $query = array()) {
  
  // get the site object. we will need it more than once
  $site = site();

  // make sure to not convert absolute urls  
  if(preg_match('!^(http|https)!i', $uri)) return $uri;

  // get the base url of the site
  $baseurl = $site->url();

  // url() can also be used to link to css, img or js files
  // so we need to make sure that this is not a link to a real
  // file. Otherwise it will be broken by the rest of the code. 
  if($uri && is_file(ROOT . DS . str_replace('/', DS, $uri))) {
    return $baseurl . '/' . $uri;          
  }

  // remove all slashes at the beginning or the end of the uri
  $uri = trim($uri, '/');

  // prepare the lang variable for later
  if(c::get('lang.support')) {
    // get the applicable language code
    $lang = ($lang) ? $lang : $site->language()->code();
    
    // prepend the language code to the uri
    $uri = trim($lang . '/' . $uri, '/');
  } 

  // if rewrite is deactivated
  // index.php needs to be prepended
  // so urls will still work
  if(!c::get('rewrite') && $uri) $uri = 'index.php/' . $uri;

  // add an additional set of parameters to the url  
  if(!empty($params)) {
    foreach($params as $key => $value) {
      $uri .= '/' . $key . ':' . $value;
    }
  }

  // add a query string to the url
  if(!empty($query)) {
    $uri .= '?' . http_build_query($query);
  }

  // If there's no URI avoid an additional slash
  // by simply returning the baseurl
  if(empty($uri)) return $baseurl;

  // return the final url and make sure 
  return $baseurl . '/' . $uri;

}

/**
 * Shortcut for the url() function
 * 
 * @see url()
 */
function u($uri=false, $lang=false) {
  return url($uri, $lang);
}

/**
 * Returns the current url with all bells and whistles
 * 
 * @return string
 */ 
function thisURL() {
  return site()->uri()->original();
}

/**
 * Redirects the user to the home page
 */
function home() {
  go(url());
}

/**
 * Redirects the user to the error page
 */
function notFound() {
  go(url(c::get('error')));
}

/**
 * Embeds a template snippet from the snippet folder
 * 
 * @param string $snippet The name of the snippet (without .php) 
 * @param array $data An optional associative array with additional data which should be accessible from within the snippet
 * @param boolean $return false: the snippet content will be echoed, true: the snippet content will be returned
 * @return string
 */ 
function snippet($snippet, $data=array(), $return=false) {
  return KirbyTemplate::loadFile(c::get('root.snippets') . DS . $snippet . '.php', $data, $return);
}

/**
 * Returns a stylesheet tag
 * 
 * @param string $url The url to the stylesheet file. Can be relative or absolute.
 * @param string $media An additional media type (i.e. screen, print, etc.)
 * @return string
 */ 
function css($url, $media=false) {
  return '<link rel="stylesheet"' . r(!empty($media), ' media="' . $media . '"') . ' href="' . url($url, false) . '" />' . "\n";
}

/**
 * Returns a javascript tag
 * 
 * @param string $url The url to the javascript file. Can be relative or absolute.
 * @param string $async adds an optional HTML5 async attribute to the script tag
 * @return string
 */ 
function js($url, $async = false) {
  return '<script' . r($async, ' async') . ' src="' . url($url, false) . '"></script>' . "\n";
}

/**
 * Returns a parameter from the URI object
 *
 * @see KirbyURI::param() 
 * @param string $key The parameter name/key
 * @param string $default The default value if the parameter could not be found or is empty
 * @return string
 */ 
function param($key, $default=false) {
  return site()->uri()->param($key, $default);
}

/**
 * Smart version of echo with an if condition as first argument
 * 
 * @param boolean $condition
 * @param string $value The string to be echoed if the condition is true
 * @param string $alternative An alternative string which should be echoed when the condition is false
 */
function e($condition, $value, $alternative = null) {
  echo r($condition, $value, $alternative);
}

/**
 * Alternative for e()
 * 
 * @see e()
 */
function ecco($condition, $value, $alternative = null) {
  e($condition, $value, $alternative);
}

/**
 * Smart version of return with an if condition as first argument
 * 
 * @param boolean $condition
 * @param string $value The string to be returned if the condition is true
 * @param string $alternative An alternative string which should be returned when the condition is false
 */
function r($condition, $value, $alternative = null) {
  return ($condition) ? $value : $alternative;
}

/**
 * Shortcut for a::show()
 * 
 * @see a::show()
 * @param mixed $variable Whatever you like to inspect
 */ 
function dump($variable) {
  a::show($variable);
}

/**
 * Raises a Kirby Exception
 * 
 * @see KirbyException
 * @param string $message An error message for the exception
 */
function raise($message) {
  require_once('exception.php');
  throw new KirbyException($message);
}

function attr($name, $value = null) {
  if(is_array($name)) {
    $attributes = array();
    foreach($name as $key => $val) {
      $a = attr($key, $val);
      if($a) $attributes[] = $a;
    }
    return implode(' ', $attributes);
  }  

  if(empty($value)) return false;
  return $name . '="' . $value . '"';    
}  