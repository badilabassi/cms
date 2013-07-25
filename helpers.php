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
 * Returns the current url with all bells and whistles
 * 
 * @return string
 */ 
function thisURL() {
  return url::current();
}

/**
 * Redirects the user to the home page
 */
function home() {
  redirect::home();
}

/**
 * Redirects the user to the error page
 */
function notFound() {
  redirect::to(site::instance()->errorPage()->url());
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
 
  $snippet = new Template(KIRBY_SITE_ROOT_SNIPPETS . DS . $snippet . '.php', $data);

  if($return) {
    return $snippet->render();
  } else {
    echo $snippet->render();
  }

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