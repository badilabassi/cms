<?php

require_once('lib/testing_bootstrap.php');

class PaginationTest extends PHPUnit_Framework_TestCase {
  public function __construct() {
    site(array(
      'currentURL' => 'http://superurl.com/tests/pagination', 
      'subfolder'  => ''
    ));
    
    $this->page       = site()->pages()->find('tests/pagination');
    $this->pages      = $this->page->children()->paginate(10);
    $this->pagination = $this->pages->pagination();
    $this->url        = $this->page->url();

  }
  
  public function testMethods() {

    $this->assertInstanceOf('KirbyPagination', $this->pagination);
    $this->assertEquals(100, $this->pagination->countItems());
    $this->assertEquals(10, $this->pagination->limit());
    $this->assertEquals(10, $this->pagination->countPages());
    $this->assertTrue($this->pagination->hasPages());
    $this->assertEquals(1, $this->pagination->page());
    $this->assertEquals(0, $this->pagination->offset());
    $this->assertEquals(1, $this->pagination->firstPage());
    $this->assertEquals(10, $this->pagination->lastPage());
    $this->assertTrue($this->pagination->isFirstPage());
    $this->assertFalse($this->pagination->isLastPage());
    $this->assertEquals(1, $this->pagination->prevPage());
    $this->assertFalse($this->pagination->hasPrevPage());
    $this->assertEquals(2, $this->pagination->nextPage());
    $this->assertTrue($this->pagination->hasNextPage());
    $this->assertEquals(1, $this->pagination->numStart());
    $this->assertEquals(10, $this->pagination->numEnd());

    $this->assertEquals($this->url, $this->pagination->firstPageURL());
    $this->assertEquals($this->url, $this->pagination->prevPageURL());
    $this->assertEquals($this->url . '/page:3', $this->pagination->pageURL(3));
    $this->assertEquals($this->url . '/page:5', $this->pagination->pageURL(5));
    $this->assertEquals($this->url . '/page:10', $this->pagination->lastPageURL());
    $this->assertEquals($this->url . '/page:2', $this->pagination->nextPageURL());

    $pagination = new KirbyPagination($this->pages, 20, array(
      'variable' => 'seite', 
      'mode'     => 'query'  
    ));
    
    $this->assertEquals($this->url . '/?seite=3', $pagination->pageURL(3));
    $this->assertEquals($this->url . '/?seite=5', $pagination->pageURL(5));
    $this->assertEquals($this->url, $pagination->firstPageURL());
    $this->assertEquals($this->url, $pagination->lastPageURL());
    $this->assertEquals($this->url, $pagination->prevPageURL());
    $this->assertEquals($this->url, $pagination->nextPageURL());

    // test the new page option
    $pagination = new KirbyPagination(200, 20, array(
      'page' => 2
    ));

    $this->assertEquals(2, $pagination->page());

  }
}