<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Defines the URL to the home page (url::home())
 */
event::on('kirby.toolkit.url.home', function(&$url, $arguments = array()) {
  // the language is the first param in the url::home() function
  $lang = a::first($arguments);
  // modify the url itself
  $url = site::instance()->url($lang);
});

/**
 * Makes the url::to(), url() and u() methods smarter and more CMS-aware
 */
event::on('kirby.toolkit.url.to', function(&$url, $arguments = array()) {

  // match arguments
  $lang   = a::get($arguments, 1);
  $params = a::get($arguments, 2, array());
  $query  = a::get($arguments, 3, array());

  // return the url for the home page
  if(!$url or $url == '/') {
    
    if(!$lang) {
      // make sure to strip the index.php for the base url
      // we don't want http://yourdomain.com/index.php
      $url = site::instance()->url();
    } else {
      $url = site::instance()->url($lang);
    }

  // search for a page this url could be for
  } else if($page = site::instance()->children()->find($url)) {
    $url = $page->url($lang);

  // simply attach the url to the base url, so links to assets and other stuff will work
  } else {
    $url = site::instance()->url() . '/' . trim($url, '/');            
  }

  // make sure to remove the trailing slash
  $url = trim($url, '/');

  // add an additional set of parameters to the url  
  if(!empty($params)) {
    $url .= '/' . url::buildParams($params);
  }

  // add a query string to the url
  if(!empty($query)) {
    $sep  = url::hasQuery($url) ? '&' : '?';
    $url .= $sep . http_build_query($query);
  }

});

/**
 * Enables auto-loading of template-specific css files
 * with css('@auto')
 */
event::on('kirby.toolkit.html.stylesheet', function(&$href, &$media = null, &$attr = array()) {

  if($href != '@auto') return false;
  
  $file = site::instance()->activePage()->template() . '.css';
  $root = c::get('css.auto.root') . DS . $file;
  $href = c::get('css.auto.url') . '/' . $file;
  
  if(!file_exists($root)) raise('The css file does not exist');
    
});

/**
 * Enables auto-loading of template-specific js files
 * with js('@auto')
 */
event::on('kirby.toolkit.html.script', function(&$src, &$attr = array()) {

  if($src != '@auto') return false;

  $file = site::instance()->activePage()->template() . '.js';
  $root = c::get('js.auto.root') . DS . $file;
  $url  = c::get('js.auto.url') . '/' . $file;

  if(!file_exists($root)) raise('The js file does not exist');

});