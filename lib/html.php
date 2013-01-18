<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Html
 * 
 * Html builder for the most common elements
 * 
 * @package Kirby CMS
 */
class Html {

  /**
   * Generates an Html tag with optional content and attributes
   * 
   * @param string $name The name of the tag, i.e. "a"
   * @param mixed $content The content if availble. Pass null to generate a self-closing tag, Pass an empty string to generate empty content
   * @param array $attr An associative array with additional attributes for the tag
   * @return string The generated Html
   */
  static public function tag($name, $content = null, $attr = array()) {

    $html = '<' . $name;
    $attr = self::attr($attr);

    if(!empty($attr)) $html .= ' ' . $attr;
    if(!is_null($content)) {
      $html .= '>' . $content . '</' . $name . '>';
    } else {
      $html .= ' />';
    }

    return $html; 

  }

  /**
   * Generates a single attribute or a list of attributes
   * 
   * @param string $name mixed string: a single attribute with that name will be generated. array: a list of attributes will be generated. Don't pass a second argument in that case. 
   * @param string $value if used for a single attribute, pass the content for the attribute here
   * @return string the generated html
   */
  static public function attr($name, $value = null) {
    if(is_array($name)) {
      $attributes = array();
      foreach($name as $key => $val) {
        $a = self::attr($key, $val);
        if($a) $attributes[] = $a;
      }
      return implode(' ', $attributes);
    }  

    if(empty($value) && $value !== '0') return false;
    return $name . '="' . $value . '"';    
  }

  /**
   * Generates an a tag
   * 
   * @param string $href The url for the a tag
   * @param mixed $text The optional text. If null, the url will be used as text
   * @param array $attr Additional attributes for the tag
   * @return string the generated html
   */
  static public function a($href, $text = null, $attr = array()) {
    $attr = array_merge(array('href' => $href), $attr);
    if(empty($text)) $text = $href;
    return self::tag('a', $text, $attr);
  }

  /**
   * Generates an "a mailto" tag
   * 
   * @param string $href The url for the a tag
   * @param mixed $text The optional text. If null, the url will be used as text
   * @param array $attr Additional attributes for the tag
   * @return string the generated html
   */
  static public function email($email, $text = null, $attr = array()) {
    $email = str::encode($email, 3);
    $attr  = array_merge(array('href' => 'mailto:' . $email), $attr);
    if(empty($text)) $text = $email;
    return self::tag('a', $text, $attr);
  }



  /**
   * Generates a div tag
   * 
   * @param string $content The content for the div
   * @param array $attr Additional attributes for the tag
   * @return string the generated html
   */
  static public function div($content, $attr = array()) {
    return self::tag('div', $content, $attr);
  }

  /**
   * Generates a p tag
   * 
   * @param string $content The content for the p tag
   * @param array $attr Additional attributes for the tag
   * @return string the generated html
   */
  static public function p($content, $attr = array()) {
    return self::tag('p', $content, $attr);
  }

  /**
   * Generates a span tag
   * 
   * @param string $content The content for the span tag
   * @param array $attr Additional attributes for the tag
   * @return string the generated html
   */
  static public function span($content, $attr = array()) {
    return self::tag('span', $content, $attr);
  }

  /**
   * Generates an img tag
   * 
   * @param string $src The url of the image
   * @param array $attr Additional attributes for the image tag
   * @return string the generated html
   */
  static public function img($src, $attr = array()) {
    $attr = array_merge(array('src' => $src, 'alt' => f::filename($src)), $attr);
    return self::tag('img', null, $attr);
  }

  /**
   * Generates an stylesheet link tag
   * 
   * @param string $href The url of the css file
   * @param string $media An optional media type (screen, print, etc.)
   * @param array $attr Additional attributes for the link tag
   * @return string the generated html
   */
  static public function stylesheet($href, $media = null, $attr = array()) {
    $attr = array_merge(array('rel' => 'stylesheet', 'href' => $href, 'media' => $media), $attr);
    return self::tag('link', null, $attr);
  }

  /**
   * Generates an script tag
   * 
   * @param string $src The url of the javascript file
   * @param boolean $async Optional HTML5 async attribute
   * @param array $attr Additional attributes for the script tag
   * @return string the generated html
   */
  static public function script($src, $async = false, $attr = array()) {
    $attr = array_merge(array('src' => $src, 'async' => r($async, 'async')), $attr);
    return self::tag('script', '', $attr);
  }

  /**
   * Generates a favicon link tag
   * 
   * @param string $href The url of the favicon file
   * @param array $attr Additional attributes for the link tag
   * @return string the generated html
   */
  static public function favicon($href, $attr = array()) {
    $attr = array_merge(array('rel' => 'shortcut icon', 'href' => $href), $attr);
    return self::tag('link', null, $attr);

  }

  /**
   * Generates an iframe
   * 
   * @param string $src The url of the iframe content
   * @param array $attr Additional attributes for the link tag
   * @param string $placeholder Text to be shown when the iframe can not be displayed.
   * @return string the generated html
   */
  static public function iframe($src, $attr = array(), $placeholder = '') {
    $attr = array_merge(array('src' => $src), $attr);    
    return self::tag('iframe', $placeholder, $attr);
  }

  /**
   * Generates the HTML5 doctype
   * 
   * @return string the generated html
   */
  static public function doctype() {
    return '<!DOCTYPE html>';
  }

  /**
   * Generates the charset metatag
   * 
   * @param string $charset
   * @return string the generated html
   */
  static public function charset($charset = 'utf-8') {
    return '<meta charset="' . $charset . '" />';
  }

  /**
   * Generates a canonical link tag
   * 
   * @param string $href The canonical url of the current page
   * @param array $attr Additional attributes for the link tag
   * @return string the generated html
   */
  static public function canonical($href, $attr = array()) {
    $attr = array_merge(array('href' => $href, 'rel' => 'canonical'), $attr);    
    return self::tag('link', null, $attr);
  }

  /**
   * Generates a HTML5 shiv script tag with additional comments for older IEs
   * 
   * @return string the generated html
   */
  static public function shiv() {
    $html  = '<!--[if lt IE 9]>' . PHP_EOL;
    $html .= '<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>' . PHP_EOL;
    $html .= '<![endif]-->' . PHP_EOL;
    return $html;
  }

  /**
   * Generates a description meta tag
   * 
   * @param string $description 
   * @param array $attr Additional attributes for the meta tag
   * @return string the generated html
   */
  static public function description($description, $attr = array()) {
    $attr = array_merge(array('name' => 'description', 'content' => $description), $attr);    
    return self::tag('meta', null, $attr);
  }

  /**
   * Generates a keywords meta tag
   * 
   * @param string $keywords 
   * @param array $attr Additional attributes for the meta tag
   * @return string the generated html
   */
  static public function keywords($keywords, $attr = array()) {
    $attr = array_merge(array('name' => 'keywords', 'content' => $keywords), $attr);    
    return self::tag('meta', null, $attr);
  }

}