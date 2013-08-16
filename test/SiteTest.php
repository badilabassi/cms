<?php

require_once('lib/bootstrap.php');

class SiteTest extends PHPUnit_Framework_TestCase {
  public function setUp() {
    $this->site = site(array(
      'url' => 'http://superurl.com',
      'root.content' => TEST_CONTENT, 
      'subfolder' => 'mysubfolder'
    ));

    $this->site->visit('tests');

  }
  
  public function tearDown() {
    site(array(
      'url'       => 'http://superurl.com', 
      'subfolder' => '',
    ));
  }
  
  public function testInitializeSite() { 
    $this->assertInstanceOf('Site', $this->site);
    $this->assertTrue($this->site instanceof Page);
  }
  
  public function testMethods() {
    $this->assertInstanceOf('Pages', $this->site->children());
    $this->assertInstanceOf('Pages', $this->site->pages());
    $this->assertEquals(3, $this->site->children()->count());
    $this->assertEquals(3, $this->site->pages()->count());
    $this->assertEquals('tests', $this->site->pages()->first()->uid());
    $this->assertEquals('home', $this->site->pages()->last()->uid());
    $this->assertFalse($this->site->troubleshoot());
    $this->assertInstanceOf('URI', $this->site->uri());
    $this->assertEquals('mysubfolder', $this->site->subfolder());
    $this->assertEquals('http://superurl.com/mysubfolder', $this->site->url());
    $this->assertTrue(in_array($this->site->scheme(), array('http', 'https')));
    $this->assertTrue(is_int($this->site->modified()));
    $this->assertTrue(is_array($this->site->index()));
    $this->assertInstanceOf('Page', $this->site->homePage());
    $this->assertEquals(c::get('home'), $this->site->homePage()->uid());
    $this->assertEquals(c::get('error'), $this->site->errorPage()->uid());
    $this->assertInstanceOf('Page', $this->site->errorPage());
    $this->assertEquals('tests', $this->site->activePage()->uid());
    $this->assertInstanceOf('Pages', $this->site->breadcrumb());
    //$this->assertFalse($this->site->hasPlugins('testplugin'));
  }
  
  public function testSiteData() {
    $this->assertEquals('Kirby', $this->site->title());
    $this->assertEquals('Bastian Allgeier', $this->site->author());
    $this->assertEquals('Kirby is awesome', $this->site->description());
    $this->assertEquals('kirby, cms, kirbycms, php, filesystem', $this->site->keywords());
    $this->assertEquals('© 2009-(date: Year) (link: http://bastianallgeier.com text: Bastian Allgeier)', $this->site->copyright());
  }
  
  public function testSiteFactory() {
    // different url and subfolder
    site(array(
      'url'       => 'http://google.com', 
      'subfolder' => ''
    ));
    
    $this->assertEquals('http://google.com', site()->url());
    $this->assertEquals('', site()->subfolder());
    
    // rewrite off
    site(array(
      'url'       => 'http://google.com', 
      'subfolder' => '', 
      'rewrite'   => false
    ));
    
    $this->assertEquals('http://google.com/index.php/tests', site()->pages()->first()->url());
    
    // rewrite off, subfolder on
    site(array(
      'url'       => 'http://google.com', 
      'subfolder' => 'maps', 
      'rewrite'   => false
    ));
    
    $this->assertEquals('http://google.com/maps/index.php/tests', site()->pages()->first()->url());
    
    // visit a page
    site(array(
      'url'        => 'http://google.com', 
      'subfolder'  => '', 
      'rewrite'    => true
    ));

    site()->visit('tests/pagination');
    
    $this->assertEquals('http://google.com/tests/pagination', site()->activePage()->url());
    
    // check for a different scheme
    site(array(
      'url'        => 'https://google.com', 
      'subfolder'  => '', 
      'rewrite'    => true
    ));

    site()->visit();
    
    $this->assertEquals('https', site()->scheme());
    $this->assertTrue(site()->ssl());
    
    // restore the defaults
    site(array(
      'url' => false, 
      'rewrite' => true, 
      'subfolder' => false, 
      'currentURL' => false,
    ));
    
    $this->assertFalse(site()->ssl());
  }

}