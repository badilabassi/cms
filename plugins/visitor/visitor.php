<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Visitor Plugin
 * 
 * Initiates the SiteVisitor object
 * and attaches it to site() 
 * 
 * @package   Kirby CMS
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://getkirby.com/license
 */
class VisitorPlugin extends Plugin {

  public function onInit($arguments = array()) {

    $this->load('lib' . DS . 'visitor.php');
    return new SiteVisitor();

  }

}