<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/** 
 * Singleton handler for the site object
 * Always use this to initiate the site!
 * 
 * You can reinitiate the site by passing 
 * the $params argument. 
 * 
 * @param array $params Additional params to overwrite/set config vars
 * @return object Site
 */
function site($params = array()) {
  return site::instance($params);
}

/**
 * Shortcut to get a page by uid
 * 
 * @param string $uid
 * @return object
 */
function page($uid = null) {
  return (is_null($uid)) ? site::instance()->activePage() : site::instance()->pages()->find($uid);
}

/**
 * Shortcut to get the $pages object
 * 
 * @return object Pages
 */
function pages() {
  return site::instance()->pages();
}

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
  
  // make sure to not convert absolute urls  
  if(preg_match('!^(http|https)!i', $uri)) {
    return $uri;

  // return the url for the home page
  } else if(!$uri or $uri == '/') {
    $url = site::instance()->url($lang);

    // make sure to strip the index.php for the base url
    // we don't want http://yourdomain.com/index.php
    if(!c::get('rewrite')) $url = rtrim($url, '/index.php');

  // search for a page this url could be for
  } else if($page = site::instance()->children()->find($uri)) {
    $url = $page->url($lang);

  // simply attach the uri to the base url, so links to assets and other stuff will work
  } else {
    $url = site::instance()->url() . '/' . trim($uri, '/');            
  }

  // make sure to remove the trailing slash
  $url = trim($url, '/');

  // add an additional set of parameters to the url  
  if(!empty($params)) {
    foreach($params as $key => $value) {
      $url .= '/' . $key . ':' . $value;
    }
  }

  // add a query string to the url
  if(!empty($query)) {
    $url .= '?' . http_build_query($query);
  }

  return $url;

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
  return site::instance()->uri()->original();
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
function snippet($snippet, $data = array(), $return = false) {
  return tpl::loadFile(KIRBY_PROJECT_ROOT_SNIPPETS . DS . $snippet . '.php', $data, $return);
}

/**
 * Returns a stylesheet tag
 * 
 * @param string $url The url to the stylesheet file. Can be relative or absolute.
 * @param string $media An additional media type (i.e. screen, print, etc.)
 * @return string
 */ 
function css($url, $media = false) {

  // auto-loading for template specific css files
  if($url == '@auto') {
  
    $file = site::instance()->children()->active()->template() . '.css';
    $root = c::get('css.auto.root') . DS . $file;
    $url  = c::get('css.auto.url') . '/' . $file;
    
    if(!file_exists($root)) return false;

  }

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

  // auto-loading for template specific js files
  if($url == '@auto') {
  
    $file = site::instance()->children()->active()->template() . '.js';
    $root = c::get('js.auto.root') . DS . $file;
    $url  = c::get('js.auto.url') . '/' . $file;

    if(!file_exists($root)) return false;

  }

  return '<script' . r($async, ' async') . ' src="' . url($url, false) . '"></script>' . "\n";
}

/**
 * Shortcut for parsing a text with the Kirbytext parser
 * 
 * @param mixed $text Variable object or string
 * @param array $params an array of options for kirbytext: array('markdown' => true, 'smartypants' => true)
 * @return string parsed text
 */
function kirbytext($text, $params = array()) {
  return Kirbytext::instance($text, $params)->get();
}

/**
 * Shortcut for parsing a text with the Kirbytext parser
 * 
 * @param mixed $text Variable object or string
 * @param array $params an array of options for kirbytext: array('markdown' => true, 'smartypants' => true)
 * @return string parsed text
 */
function kt($text, $params = array()) {
  return Kirbytext::instance($text, $params)->get();
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

  require_once(KIRBY_CMS_ROOT_PARSERS . DS . 'yaml.php');

  function yaml($string) {
    return spyc_load(trim($string));
  }

}

/**
 * Creates an excerpt without html and kirbytext
 * 
 * @param mixed $text Variable object or string
 * @param int $length The number of characters which should be included in the excerpt
 * @param array $params an array of options for kirbytext: array('markdown' => true, 'smartypants' => true)
 * @return string The shortened text
 */
function excerpt($text, $length = 140, $params = array()) {
  return str::excerpt(Kirbytext::instance($text, $params)->get(), $length);
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
function flash($url, $width = null, $height = null) {
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
function twitter($username, $text = null, $title = null, $class = null) {
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
function gist($url, $file = null) {
  return Kirbytext::instance()->tag('gist', $url, array(
    'file' => $file
  ));
}