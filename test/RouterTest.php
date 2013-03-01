<?php

require_once('lib/testing_bootstrap.php');

class RouterTest extends PHPUnit_Framework_TestCase {
  
  public function testAbsoluteRoutes() {

    site(array(
      'currentURL' => 'contact'
    ));

    site()->router()->add('contact', array(
      'page' => 'tests/page/subpage-1'
    ));

    $this->assertEquals('tests/page/subpage-1', site()->activePage()->uri());    

  }

  public function testWildcard() {

    site(array(
      'currentURL' => 'blog/2012/11/10'
    ));

    site()->router()->add('blog/*', array(
      'page' => 'tests/page/subpage-1'
    ));

    $this->assertEquals('tests/page/subpage-1', site()->activePage()->uri());    

  }

  public function testParams() {

    site(array(
      'currentURL' => 'blog/2012'
    ));

    site()->router()->add('blog/@year', array(
      'page' => 'tests/page/subpage-1'
    ));

    $this->assertEquals('tests/page/subpage-1', site()->activePage()->uri());    
    $this->assertEquals(array('year' => 2012), site()->router()->params());
    $this->assertEquals(2012, site()->router()->params('year'));
    $this->assertEquals(2012, site()->router()->route()->params('year'));

  }

  public function testOptionalParams() {

    site(array(
      'currentURL' => 'blog/2012/12'
    ));

    site()->router()->add('blog(/@year(/@month(/@day)))', array(
      'page' => 'tests/page/subpage-1'
    ));

    $this->assertEquals('tests/page/subpage-1', site()->activePage()->uri());    
    $this->assertEquals(array('year' => 2012, 'month' => 12), site()->router()->params());


    site(array(
      'currentURL' => 'blog/2012/12/10'
    ));

    site()->router()->add('blog(/@year(/@month(/@day)))', array(
      'page' => 'tests/page/subpage-1'
    ));

    $this->assertEquals('tests/page/subpage-1', site()->activePage()->uri());    
    $this->assertEquals(array('year' => 2012, 'month' => 12, 'day' => 10), site()->router()->params());

  }

  public function testRegex() {

    site(array(
      'currentURL' => 'blog/2012/12'
    ));

    site()->router()->add('blog/@year:[0-9]{4}/@month:[0-9]{2}', array(
      'page' => 'tests/page/subpage-1'
    ));

    $this->assertEquals('tests/page/subpage-1', site()->activePage()->uri());    
    $this->assertEquals(array('year' => 2012, 'month' => 12), site()->router()->params());

    site(array(
      'currentURL' => 'blog/test/will-fail'
    ));

    site()->router()->add('blog/@year:[0-9]{4}/@month:[0-9]{2}', array(
      'page' => 'tests/page/subpage-1'
    ));

    $this->assertFalse('tests/page/subpage-1' == site()->activePage()->uri());    
    $this->assertFalse(array('year' => 2012, 'month' => 12) == site()->router()->params());

  }

  public function testMethod() {

    site(array(
      'currentURL' => 'blog/2012/12'
    ));

    site()->router()->add('blog/@year:[0-9]{4}/@month:[0-9]{2}', array(
      'page'    => 'tests/page/subpage-1',
      'methods' => 'POST'
    ));

    $this->assertFalse('tests/page/subpage-1' == site()->activePage()->uri());    
    $this->assertFalse(array('year' => 2012, 'month' => 12) == site()->router()->params());

  }

}