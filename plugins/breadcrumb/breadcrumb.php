<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Breadcrumb Plugin
 * 
 * Initiates the breadcrumb object
 * and attaches it to site() 
 * 
 * @package   Kirby CMS
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://getkirby.com/license
 */
class BreadcrumbPlugin extends Plugin {

  /**
   * Calls the crumb method as soon as the
   * plugin is initiated so it can be accessed with
   * site()->breadcrumb()
   */
  public function onInit($arguments = array()) {
    return $this->crumb();
  }

  /**
   * The crumb method creates the current 
   * breadcrumb and returns a new Pages collection
   * 
   * @return object Pages
   */
  protected function crumb() {

    $site  = site();
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
    return new Pages($crumb);

  }

}