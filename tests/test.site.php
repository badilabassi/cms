<?php

require_once('bootstrap.php');

class TestOfSite extends UnitTestCase {

  function testInitializeSite() {

    $this->site = site(array(
      'url' => 'http://superurl.com',
      'currentURL' => 'http://superurl.com/tests',
      'root.content' => TEST_CONTENT, 
      'subfolder' => 'mysubfolder'
    ));

    $this->assertIsA($this->site, 'KirbySite');
    $this->assertTrue($this->site instanceof KirbyPage);

  }

  function testMethods() {

    $this->assertIsA($this->site->children(), 'KirbyPages');
    $this->assertIsA($this->site->pages(), 'KirbyPages');
    $this->assertTrue($this->site->children()->count(), 6);
    $this->assertTrue($this->site->pages()->count(), 6);
    $this->assertTrue($this->site->pages()->first()->uid() == 'tests');
    $this->assertTrue($this->site->pages()->last()->uid() == 'home');
    $this->assertIsA($this->site->load(), 'KirbyLoader');
    $this->assertFalse($this->site->troubleshoot());
    $this->assertIsA($this->site->uri(), 'KirbyURI');
    $this->assertTrue($this->site->subfolder() == 'mysubfolder');
    $this->assertTrue($this->site->url() == 'http://superurl.com/mysubfolder');
    $this->assertTrue(in_array($this->site->scheme(), array('http', 'https')));
    $this->assertTrue(is_int($this->site->modified()));
    $this->assertTrue(is_array($this->site->index()));
    $this->assertIsA($this->site->homePage(), 'KirbyPage');
    $this->assertTrue($this->site->homePage()->uid() == c::get('home'));
    $this->assertTrue($this->site->errorPage()->uid() == c::get('error'));
    $this->assertIsA($this->site->errorPage(), 'KirbyPage');
    $this->assertTrue($this->site->activePage()->uid() == 'tests');
    $this->assertIsA($this->site->breadcrumb(), 'KirbyPages');
    $this->assertFalse($this->site->hasPlugins('testplugin'));

  }

  function testSiteData() {

    $this->assertTrue($this->site->title() == 'Kirby');
    $this->assertTrue($this->site->author() == 'Bastian Allgeier');
    $this->assertTrue($this->site->description() == 'Kirby is awesome');
    $this->assertTrue($this->site->keywords() == 'kirby, cms, kirbycms, php, filesystem');
    $this->assertTrue($this->site->copyright() == '© 2009-(date: Year) (link: http://bastianallgeier.com text: Bastian Allgeier)');

  }

  function testSiteFactory() {

    // different url and subfolder
    site(array(
      'url'       => 'http://google.com', 
      'subfolder' => ''
    ));

    $this->assertTrue(site()->url() == 'http://google.com');
    $this->assertTrue(site()->subfolder() == '');

    // rewrite off
    site(array(
      'url'       => 'http://google.com', 
      'subfolder' => '', 
      'rewrite'   => false
    ));

    $this->assertTrue(site()->pages()->first()->url() == 'http://google.com/index.php/tests');

    // rewrite off, subfolder on
    site(array(
      'url'       => 'http://google.com', 
      'subfolder' => 'maps', 
      'rewrite'   => false
    ));

    $this->assertTrue(site()->pages()->first()->url() == 'http://google.com/maps/index.php/tests');

    // visit a page
    site(array(
      'url'        => 'http://google.com', 
      'subfolder'  => '', 
      'currentURL' => 'tests/pagination', 
      'rewrite'    => true
    ));

    $this->assertTrue(site()->activePage()->url() == 'http://google.com/tests/pagination');

    // check for a different scheme
    site(array(
      'url'        => 'https://google.com', 
      'subfolder'  => '', 
      'currentURL' => 'https://google.com', 
      'rewrite'    => true
    ));

    $this->assertTrue(site()->scheme() == 'https');
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