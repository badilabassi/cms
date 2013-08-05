<?php

require_once('lib/bootstrap.php');

class PagesTest extends PHPUnit_Framework_TestCase {
  public function __construct() {
    c::set('lang.support', false);
    $pages = array(
      site()->pages()->find('tests/page/subpage-1'),
      site()->pages()->find('tests/page/subpage-2'),
      site()->pages()->find('tests/page/subpage-3'),
      site()->pages()->find('tests/page/visible-subpage-1'),
      site()->pages()->find('tests/page/visible-subpage-2'),
      site()->pages()->find('tests/page/visible-subpage-3'),
    );
    $this->pages = new Pages($pages);
  }

  public function testMethods() {
    $this->assertInstanceOf('Pages', $this->pages);
    
    $this->assertEquals(6, $this->pages->count());
    $this->assertEquals('subpage-1', $this->pages->first()->uid());
    $this->assertEquals('subpage-2', $this->pages->nth(1)->uid());
    $this->assertEquals('visible-subpage-3', $this->pages->last()->uid());
    
    // test the without method
    $pages = $this->pages->not('tests/page/subpage-3');
    
    $this->assertEquals(5, $pages->count());
    $this->assertEquals('subpage-1', $pages->first()->uid());
    $this->assertEquals('visible-subpage-3', $pages->last()->uid());
    
    //$this->assertTrue($pages->active()->isHomePage());
      
    $this->assertEquals('visible-subpage-3', $this->pages->visible()->last()->uid());
    $this->assertEquals(3, $this->pages->visible()->count());
    $this->assertEquals($this->pages->countVisible(), $this->pages->visible()->count());
    $this->assertEquals('subpage-1', $this->pages->invisible()->first()->uid());
    $this->assertEquals('subpage-3', $this->pages->invisible()->last()->uid());
    $this->assertEquals(3, $this->pages->invisible()->count());
    $this->assertEquals($this->pages->countInvisible(), $this->pages->invisible()->count());
    
    $this->assertInstanceOf('Pages', $this->pages->children());
    $this->assertEquals(1, $this->pages->children()->count());
    $this->assertEquals($this->pages->countChildren(), $this->pages->children()->count());
    
    // find
    $this->assertEquals('subpage-1', $this->pages->find('subpage-1')->title());
    $this->assertEquals('visible-subpage-1', $this->pages->find('visible-subpage-1')->uid());
    $this->assertEquals(null, $this->pages->find('not-existing'));
    
    // multiple finds
    $this->assertInstanceOf('Pages', $this->pages->find('subpage-1', 'visible-subpage-2', 'visible-subpage-3'));
    $this->assertEquals(3, $this->pages->find('subpage-1', 'visible-subpage-2', 'visible-subpage-3')->count());
    
    // find by uid
    $this->assertEquals('subpage-1', $this->pages->findByUID('subpage-1')->title());
    
    // find by uids
    $this->assertEquals('subpage-2', $this->pages->findByUID('subpage-1', 'subpage-2')->last()->title());
    
    // find by dirname
    $this->assertEquals('visible-subpage-2', $this->pages->findByDirname('02-visible-subpage-2')->title());
    
    // find by dirnames
    $this->assertEquals('visible-subpage-1', $this->pages->findByDirname('01-visible-subpage-1', 'subpage-1')->first()->title());
    
    // find by title
    $this->assertEquals('subpage-1', $this->pages->findByTitle('subpage-1')->dirname());
    
    // find by titles
    $this->assertEquals('subpage-2', $this->pages->findByTitle('subpage-1', 'subpage-2')->last()->title());
    
    // TODO: filterBy tests
  }
}