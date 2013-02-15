<?php

require_once('bootstrap.php');

class TestOfPages extends UnitTestCase {

  function __construct() {
    
    c::set('lang.support', false);

    $pages = array(
      site()->pages()->find('tests/page/subpage-1'),
      site()->pages()->find('tests/page/subpage-2'),
      site()->pages()->find('tests/page/subpage-3'),
      site()->pages()->find('tests/page/visible-subpage-1'),
      site()->pages()->find('tests/page/visible-subpage-2'),
      site()->pages()->find('tests/page/visible-subpage-3'),
    );

    $this->pages = new KirbyPages($pages);
  
  }

  function testMethods() {

    $this->assertIsA($this->pages, 'KirbyPages');

    $this->assertTrue($this->pages->count() == 6);
    $this->assertTrue($this->pages->first()->uid() == 'subpage-1');
    $this->assertTrue($this->pages->nth(1)->uid() == 'subpage-2');
    $this->assertTrue($this->pages->last()->uid() == 'visible-subpage-3');

    // test the without method
    $pages = $this->pages->without('tests/page/subpage-3');

    $this->assertTrue($pages->count() == 5);
    $this->assertTrue($pages->first()->uid() == 'subpage-1');
    $this->assertTrue($pages->last()->uid() == 'visible-subpage-3');

    //$this->assertTrue($pages->active()->isHomePage());
    
    $this->assertTrue($this->pages->visible()->last()->uid() == 'visible-subpage-3');
    $this->assertTrue($this->pages->visible()->count() == 3);
    $this->assertTrue($this->pages->visible()->count() == $this->pages->countVisible());
    $this->assertTrue($this->pages->invisible()->first()->uid() == 'subpage-1');
    $this->assertTrue($this->pages->invisible()->last()->uid() == 'subpage-3');
    $this->assertTrue($this->pages->invisible()->count() == 3);
    $this->assertTrue($this->pages->invisible()->count() == $this->pages->countInvisible());

    $this->assertIsA($this->pages->children(), 'KirbyPages');
    $this->assertTrue($this->pages->children()->count() == 1);
    $this->assertTrue($this->pages->children()->count() == $this->pages->countChildren());

    // find
    $this->assertTrue($this->pages->find('subpage-1')->title() == 'subpage-1');
    $this->assertTrue($this->pages->find('visible-subpage-1')->uid() == 'visible-subpage-1');
    $this->assertTrue($this->pages->find('not-existing') == null);

    // multiple finds
    $this->assertIsA($this->pages->find('subpage-1', 'visible-subpage-2', 'visible-subpage-3'), 'KirbyPages');
    $this->assertTrue($this->pages->find('subpage-1', 'visible-subpage-2', 'visible-subpage-3')->count() == 3);

    // find by uid
    $this->assertTrue($this->pages->findByUID('subpage-1')->title() == 'subpage-1');

    // find by uids
    $this->assertTrue($this->pages->findByUID('subpage-1', 'subpage-2')->last()->title() == 'subpage-2');

    // find by dirname
    $this->assertTrue($this->pages->findByDirname('02-visible-subpage-2')->title() == 'visible-subpage-2');

    // find by dirnames
    $this->assertTrue($this->pages->findByDirname('01-visible-subpage-1', 'subpage-1')->first()->title() == 'visible-subpage-1');

    // find by title
    $this->assertTrue($this->pages->findByTitle('subpage-1')->dirname() == 'subpage-1');

    // find by titles
    $this->assertTrue($this->pages->findByTitle('subpage-1', 'subpage-2')->last()->title() == 'subpage-2');

    // TODO: filterBy tests

  }

}