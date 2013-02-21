<?php

require_once('testing_bootstrap.php');

class PageTest extends PHPUnit_Framework_TestCase {
  public function __construct() {
    $this->root = TEST_CONTENT . '/01-tests/page';
    $this->uid  = 'page';
    $this->page = site()->pages()->find('tests/page');
  }
  
  public function testGetters() {
    $this->assertInstanceOf('KirbyPage', $this->page);
    
    $this->assertTrue(method_exists($this->page, 'children'));
    $this->assertTrue(method_exists($this->page, 'parent'));
    $this->assertTrue(method_exists($this->page, 'uri'));
    $this->assertTrue(method_exists($this->page, 'dirname'));
    $this->assertTrue(method_exists($this->page, 'root'));
    $this->assertTrue(method_exists($this->page, 'diruri'));
    $this->assertTrue(method_exists($this->page, 'template'));
    $this->assertTrue(method_exists($this->page, 'intendedTemplate'));
    $this->assertTrue(method_exists($this->page, 'content'));
  }
  
  public function testMethods() {
    // check if the page exists
    $this->assertTrue($this->page->exists());
    
    // check the modified date
    $this->assertEquals(filectime($this->root), is_int($this->page->modified()) && $this->page->modified() > 0 && $this->page->modified());
    
    // check for the scanned inventory
    $this->assertInstanceOf('KirbyDir', $this->page->dir());
    
    // check for children
    $this->assertInstanceOf('KirbyPages', $this->page->children());
    
    // check if this is a child of
    $this->assertTrue($this->page->isChildOf($this->page->parent()));
    
    // check if this is a descendant of
    $this->assertTrue(site()->pages()->find('tests/page/subpage-1')->isDescendantOf(site()->pages()->find('tests')));
    
    // check if this is a ancestor of
    $this->assertTrue(site()->pages()->find('tests')->isAncestorOf(site()->pages()->find('tests/page/subpage-1')));
    
    // so far there should be three children
    $this->assertEquals(6, $this->page->children()->count());
    
    // count children
    $this->assertEquals(6, $this->page->countChildren());
    
    // has children
    $this->assertTrue($this->page->hasChildren());
    
    // check for siblings
    $this->assertInstanceOf('KirbyPages', $this->page->siblings());
    
    // count siblings
    $this->assertTrue($this->page->countSiblings() > 0);
    
    // has children
    $this->assertTrue($this->page->hasSiblings());
    
    // is this page visible => should be false
    $this->assertFalse($this->page->isVisible());
    
    // is this page invisible => should be true
    $this->assertTrue($this->page->isInvisible());
  }
  
  public function testRootIsSet() {
    $this->assertEquals(realpath($this->root), $this->page->root());
  }
  
  public function testDirname() {
    $this->assertEquals(basename($this->root), $this->page->dirname());
  }
  
  public function testUID() {
    $this->assertEquals($this->uid, $this->page->uid());
  }
  
  public function testParent() {
    $parent = $this->page->parent();
    
    $this->assertEquals(dirname($this->page->root()), $parent->root());
    
    $parent = $this->page->parent();
    $this->assertEquals(dirname($this->page->root()), $parent->root());
  }
  
  public function testPrevNext() {
    $p = site()->pages()->find('tests/page/subpage-2');
    
    $this->assertEquals('subpage-1', $p->prev()->uid());
    $this->assertEquals('subpage-3', $p->next()->uid());
    
    $this->assertEquals('visible-subpage-3', $p->prev()->prev()->uid());
    $this->assertNull($p->next()->next());
    
    $this->assertTrue($p->hasPrev());
    $this->assertTrue($p->hasNext());
    
    $this->assertFalse($p->hasPrevVisible());
    $this->assertTrue($p->hasPrevInvisible());
    $this->assertTrue($p->hasNextVisible());
    $this->assertTrue($p->hasNextInvisible());
  }
  
  public function testDepth() {
    $p = site()->pages()->find('tests/page/subpage-1');
    $this->assertEquals(3, $p->depth());
    
    $p = site()->pages()->find('tests');
    $this->assertEquals(1, $p->depth());
    
    $this->assertEquals(0, site()->depth());
    
    $p = site()->pages()->find('tests/page/subpage-1/subsubpage-1');
    
    $this->assertEquals(3, $p->parents()->count());
    $this->assertEquals('tests', $p->parents()->last()->uid());
  }
  
  public function testEmptyPage() {
    $p = site()->pages()->find('tests/empty');
    $this->assertEquals('empty', $p->title());
  }
  
  public function testHomePage() {
    site(array(
      'home' => 'tests'
    ));
    
    $this->assertTrue(site()->pages()->find('tests')->isHomePage());
    $this->assertFalse(site()->pages()->find('home')->isHomePage());
    
    site(array(
      'home' => 'home'
    ));
    
    $this->assertTrue(site()->pages()->find('home')->isHomePage());
    $this->assertFalse(site()->pages()->find('tests')->isHomePage());
  }

  public function testErrorPage() {
    site(array(
      'error' => 'tests'
    ));
    
    $this->assertTrue(site()->pages()->find('tests')->isErrorPage());
    $this->assertFalse(site()->pages()->find('error')->isErrorPage());
    
    site(array(
      'error' => 'error'
    ));
    
    $this->assertTrue(site()->pages()->find('error')->isErrorPage());
    $this->assertFalse(site()->pages()->find('tests')->isErrorPage());
  }
}