<?php

require_once('bootstrap.php');

class TestOfPage extends UnitTestCase {

  function __construct() {
    $this->root = TEST_CONTENT . '/01-tests/page';
    $this->uid  = 'page';
    $this->page = site()->pages()->find('tests/page');
  }

  function testGetters() {

    $this->assertIsA($this->page, 'KirbyPage');
    
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

  function testMethods() {

    // check if the page exists
    $this->assertTrue($this->page->exists());

    // check the modified date
    $this->assertTrue(is_int($this->page->modified()) && $this->page->modified() > 0 && $this->page->modified() == filectime($this->root));

    // check for the scanned inventory
    $this->assertIsA($this->page->dir(), 'KirbyDir');

    // check for children
    $this->assertIsA($this->page->children(), 'KirbyPages');

    // check if this is a child of
    $this->assertTrue($this->page->isChildOf($this->page->parent()));

    // check if this is a descendant of
    $this->assertTrue(site()->pages()->find('tests/page/subpage-1')->isDescendantOf(site()->pages()->find('tests')));

    // check if this is a ancestor of
    $this->assertTrue(site()->pages()->find('tests')->isAncestorOf(site()->pages()->find('tests/page/subpage-1')));

    // so far there should be three children
    $this->assertTrue($this->page->children()->count() == 6);

    // count children
    $this->assertTrue($this->page->countChildren() == 6);

    // has children
    $this->assertTrue($this->page->hasChildren());

    // check for siblings
    $this->assertIsA($this->page->siblings(), 'KirbyPages');

    // count siblings
    $this->assertTrue($this->page->countSiblings() > 0);

    // has children
    $this->assertTrue($this->page->hasSiblings());

    // is this page visible => should be false
    $this->assertFalse($this->page->isVisible());

    // is this page invisible => should be true
    $this->assertTrue($this->page->isInvisible());
    
  }

  function testRootIsSet() {
    $this->assertTrue($this->page->root() == realpath($this->root));
  }

  function testDirname() {
    $this->assertTrue($this->page->dirname() == basename($this->root));
  }

  function testUID() {
    $this->assertTrue($this->page->uid() == $this->uid);
  }

  function testParent() {

    $parent = $this->page->parent();

    $this->assertTrue($parent->root() == dirname($this->page->root()));

    $parent = $this->page->parent();
    $this->assertTrue($parent->root() == dirname($this->page->root()));
            
  }

  function testPrevNext() {

    $p = site()->pages()->find('tests/page/subpage-2');

    $this->assertTrue($p->prev()->uid() == 'subpage-1');
    $this->assertTrue($p->next()->uid() == 'subpage-3');

    $this->assertTrue($p->prev()->prev()->uid() == 'visible-subpage-3');
    $this->assertTrue($p->next()->next() == null);

    $this->assertTrue($p->hasPrev());
    $this->assertTrue($p->hasNext());

  }

  function testDepth() {

    $p = site()->pages()->find('tests/page/subpage-1');
    $this->assertTrue($p->depth() == 3);

    $p = site()->pages()->find('tests');
    $this->assertTrue($p->depth() == 1);

    $this->assertTrue(site()->depth() == 0);

    $p = site()->pages()->find('tests/page/subpage-1/subsubpage-1');

    $this->assertTrue($p->parents()->count() == 3);
    $this->assertTrue($p->parents()->last()->uid() == 'tests');

  }

  /**
   * Tests a page without any content
   */
  function testEmptyPage() {

    $p = site()->pages()->find('tests/empty');
    $this->assertTrue($p->title() == 'empty');

  }

}