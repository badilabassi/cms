<?php

require_once('bootstrap.php');

class TestOfPagination extends UnitTestCase {

  function __construct() {

    site(array(
      'currentURL' => 'http://superurl.com/tests/pagination', 
      'subfolder'  => ''
    ));

    $this->page  = site()->pages()->find('tests/pagination');
    $this->pages = $this->page->children()->paginate(10);
    $this->pagination = $this->pages->pagination();
    $this->url   = $this->page->url();
    
  }

  function testMethods() {

    $this->assertTrue($this->pagination->countItems() == 100);
    $this->assertTrue($this->pagination->limit() == 10);
    $this->assertTrue($this->pagination->countPages() == 10);
    $this->assertTrue($this->pagination->hasPages());
    $this->assertTrue($this->pagination->page() == 1);
    $this->assertTrue($this->pagination->offset() == 0);
    $this->assertTrue($this->pagination->firstPage() == 1);
    $this->assertTrue($this->pagination->lastPage() == 10);
    $this->assertTrue($this->pagination->isFirstPage());
    $this->assertFalse($this->pagination->isLastPage());
    $this->assertTrue($this->pagination->prevPage() == 1);
    $this->assertFalse($this->pagination->hasPrevPage());
    $this->assertTrue($this->pagination->nextPage() == 2);
    $this->assertTrue($this->pagination->hasNextPage());
    $this->assertTrue($this->pagination->numStart() == 1);
    $this->assertTrue($this->pagination->numEnd() == 10);

    $this->assertTrue($this->pagination->pageURL(3) == $this->url . '/page:3');
    $this->assertTrue($this->pagination->pageURL(5) == $this->url . '/page:5');
    $this->assertTrue($this->pagination->firstPageURL() == $this->url . '/page:1');
    $this->assertTrue($this->pagination->lastPageURL() == $this->url . '/page:10');
    $this->assertTrue($this->pagination->prevPageURL() == $this->url . '/page:1');
    $this->assertTrue($this->pagination->nextPageURL() == $this->url . '/page:2');

    $pagination = new KirbyPagination($this->pages, 20, array(
      'variable' => 'seite', 
      'mode'     => 'query'  
    ));

    $this->assertTrue($pagination->pageURL(3) == $this->url . '/?seite=3');
    $this->assertTrue($pagination->pageURL(5) == $this->url . '/?seite=5');
    $this->assertTrue($pagination->firstPageURL() == $this->url . '/?seite=1');
    $this->assertTrue($pagination->lastPageURL() == $this->url . '/?seite=1');
    $this->assertTrue($pagination->prevPageURL() == $this->url . '/?seite=1');
    $this->assertTrue($pagination->nextPageURL() == $this->url . '/?seite=1');

  }

}