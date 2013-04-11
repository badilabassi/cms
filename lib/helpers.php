<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

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
  return tpl::loadFile(ROOT_SITE_SNIPPETS . DS . $snippet . '.php', $data, $return);
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
 * Raises a Kirby Exception
 * 
 * @see KirbyException
 * @param string $message An error message for the exception
 */
function raise($message) {
  require_once('exception.php');
  throw new KirbyException($message);
}

/**
 * Shortcut for parsing a text with the Kirbytext parser
 * 
 * @param mixed $text KirbyVariable object or string
 * @param array $options an array of options for kirbytext: array('markdown' => true, 'smartypants' => true)
 * @return string parsed text
 */
function kirbytext($text, $options = array()) {
  return Kirbytext::instance($text, $options)->get();
}

/**
 * Shortcut for parsing a text with the Kirbytext parser
 * 
 * @param mixed $text KirbyVariable object or string
 * @param array $options an array of options for kirbytext: array('markdown' => true, 'smartypants' => true)
 * @return string parsed text
 */
function kt($text, $options = array()) {
  return Kirbytext::instance($text, $options)->get();
}

/**
 * Shortcut for markdown()
 * 
 * @param string $text
 * @return string parsed text
 */
function md($text) {
  return markdown($text);
}

/**
 * Parses yaml structured text
 * 
 * @param string $text
 * @return string parsed text
 */
if(!function_exists('yaml')) {

  require_once(ROOT_KIRBY_PARSERS . DS . 'yaml.php');

  function yaml($string) {
    return spyc_load(trim($string));
  }

}

/**
 * Creates an excerpt without html and kirbytext
 * 
 * @param mixed $text KirbyVariable object or string
 * @param int $length The number of characters which should be included in the excerpt
 * @param array $options an array of options for kirbytext: array('markdown' => true, 'smartypants' => true)
 * @return string The shortened text
 */
function excerpt($text, $length = 140, $options = array()) {
  return str::excerpt(Kirbytext::instance($text, $options)->get(), $length);
}

/**
 * Embeds a Youtube video by url
 * 
 * @param string $url The Youtube url, ie. http://www.youtube.com/watch?v=d9NF2edxy-M
 * @param int $width The width of the embedded video
 * @param int $height The height of the embedded video
 * @param string $class an additional class selector which should be added to the iframe
 * @return string The generated html
 */
function youtube($url, $width = false, $height = false, $class = false) {
  return Kirbytext::instance()->tag('youtube', $url, array(
    'width'  => $width,
    'height' => $height,
    'class'  => $class
  ));
}

/**
 * Embeds a Vimeo video by url
 * 
 * @param string $url The Vimeo url, ie. http://vimeo.com/52345557
 * @param int $width The width of the embedded video
 * @param int $height The height of the embedded video
 * @param string $class an additional class selector which should be added to the iframe
 * @return string The generated html
 */
function vimeo($url, $width = false, $height = false, $class = false) {
  return Kirbytext::instance()->tag('vimeo', $url, array(
    'width'  => $width,
    'height' => $height,
    'class'  => $class
  ));
}

/**
 * Embeds a flash file
 * 
 * @param string $url the url for the fla or swf file
 * @param int $width
 * @param int $height 
 * @return string
 */
function flash($url, $width = false, $height = false) {
  return Kirbytext::instance()->tag('flash', $url, array(
    'width'  => $width, 
    'height' => $height
  ));
}

/**
 * Generates a link to a twitter profile
 * 
 * @param string $username Twitter username (with or without prepended @)
 * @param string $text Optional Link text. If not specified the username will be used
 * @param string $title Optional link title
 * @param string $class Optional class selector for the a tag
 * @return string twitter link
 */
function twitter($username, $text = false, $title = false, $class = false) {
  return Kirbytext::instance()->tag('twitter', $username, array(
    'text'    => $text,
    'title'   => $title,
    'class'   => $class
  ));
}

/**
 * Embeds a github gist
 * 
 * @param string $url Gist url: i.e. https://gist.github.com/2924148
 * @param string $file The name of a particular file from the gist, which should displayed only. 
 * @return string
 */
function gist($url, $file = false) {
  return Kirbytext::instance()->tag('gist', $url, array(
    'file' => $file
  ));
}

/**
 * displays past times in a human readble format (i.e. 2 years ago)
 * 
 * @param mixed $date Either a unix timestamp or KirbyDate object
 * @return string
 */
function ago($date = null) {

  // transfer timestamps first
  if(!is_a($date, 'KirbyDate')) $date = new KirbyDate($date);
  return $date->ago();
    
}


function objectify($array) {

  $result = new KirbyCollection();

  foreach($array as $key => $value) {
    if(is_array($value) || is_object($value)) {
      $result->$key = objectify($value);
    } else {
      $result->$key = new KirbyString($value);
    }
  }

  return $result;

}

