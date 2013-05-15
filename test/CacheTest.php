<?php

require_once('lib/bootstrap.php');

class CacheTest extends PHPUnit_Framework_TestCase {
    
  public function testIgnored() {

    /*
    c::set('cache.ignore.urls', array(
      'home', 
      'projects*',
      'contact/*', 
      'about-us'
    ));

    $this->assertTrue(site()->cache()->isIgnored('home'));
    $this->assertTrue(cache::ignored('projects'));
    $this->assertTrue(cache::ignored('projects/subproject'));
    $this->assertTrue(cache::ignored('projects/subproject/subsubproject'));
    $this->assertTrue(cache::ignored('about-us'));
    $this->assertFalse(cache::ignored('some-other-page'));

    c::set('cache.ignore.urls', array());

    c::set('cache.ignore.templates', array(
      'template-a', 
      'template-b',
    ));

    $this->assertTrue(cache::ignored('home', 'template-a'));
    $this->assertTrue(cache::ignored('home', 'template-b'));
    $this->assertFalse(cache::ignored('home', 'template-c'));
    */

  }

}