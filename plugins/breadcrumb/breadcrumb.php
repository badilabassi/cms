<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

// add the breadcrumb collection to the site object
site::extend('breadcrumb', function($site) {

  static $breadcrumb;

  if(is_null($breadcrumb)) {

    $path  = $site->uri()->path()->toArray(); 
    $crumb = array();
  
    foreach($path AS $p) {
      $tmp  = implode('/', $path);
      $data = $site->pages()->find($tmp);
            
      if(!$data || $data->isErrorPage()) {
        // add the error page to the crumb
        $crumb[] = $site->errorPage();
        // don't move on with subpages, because there won't be 
        // any if the first page hasn't been found at all
        break;
      } else {      
        $crumb[] = $data;
      }
      array_pop($path);        
    }
    
    // we've been moving through the uri from tail to head
    // so we need to reverse the array to get a proper crumb    
    $crumb = array_reverse($crumb);   

    // add the homepage to the beginning of the crumb array
    if(c::get('breadcrumb.home') !== false) array_unshift($crumb, $site->homePage());
    
    // make it a pages object so we can handle it
    // like we handle all pages on the site  
    $breadcrumb = new Pages($crumb);

  }

  return $breadcrumb;

});