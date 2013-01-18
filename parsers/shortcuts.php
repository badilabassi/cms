<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Creates safe html by encoding special characters
 * 
 * @param string $text unencoded text
 * @return string
 */
function html($text) {
  return str::html($text, false);
}

/**
 * Shortcut for html()
 * 
 * @see html()
 */
function h($text) {
  return html($text);
}

/**
 * Creates safe xml by encoding special characters
 * 
 * @param string $text unencoded text
 * @return string
 */
function xml($text) {
  return str::xml($text);
}

/**
 * Converts new lines to html breaks
 * 
 * @param string $text with new lines
 * @return string
 */
function multiline($text) {
  return nl2br(html($text));
}

/**
 * Shortcut for parsing a text with the Kirbytext parser
 * 
 * @param mixed $text KirbyVariable object or string
 * @param boolean $mdown true: markdown is enabled, false: markdown is disabled
 * @param boolean $smartypants true: smartypants is enabled, false: smartypants is disabled
 * @return string parsed text
 */
function kirbytext($text, $markdown = true, $smartypants = true) {
  return Kirbytext::instance($text, $markdown, $smartypants)->get();
}

/**
 * Shortcut for parsing a text with the Kirbytext parser
 * 
 * @param mixed $text KirbyVariable object or string
 * @param boolean $mdown true: markdown is enabled, false: markdown is disabled
 * @param boolean $smartypants true: smartypants is enabled, false: smartypants is disabled
 * @return string parsed text
 */
function kt($text, $markdown = true, $smartypants = true) {
  return Kirbytext::instance($text, $markdown, $smartypants)->get();
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
function yaml($string) {
  return spyc_load(trim($string));
}

/**
 * Creates an excerpt without html and kirbytext
 * 
 * @param mixed $text KirbyVariable object or string
 * @param int $length The number of characters which should be included in the excerpt
 * @param boolean $markdown If true, markdown will be parsed first before creating the excerpt
 * @param boolean $smartypants If true, smartypants will be parsed first before creating the excerpt
 * @return string The shortened text
 */
function excerpt($text, $length = 140, $markdown = true, $smartypants = true) {
  return str::excerpt(Kirbytext::instance($text, $markdown, $smartypants)->get(), $length);
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
  return Kirbytext::instance()->youtube(array(
    'youtube' => $url,
    'width'   => $width,
    'height'  => $height,
    'class'   => $class
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
  return Kirbytext::instance()->vimeo(array(
    'vimeo'  => $url,
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
  return Kirbytext::instance()->flash($url, $width, $height);
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
  return Kirbytext::instance()->twitter(array(
    'twitter' => $username,
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
  return Kirbytext::instance()->gist(array(
    'gist' => $url,
    'file' => $file
  ));
}