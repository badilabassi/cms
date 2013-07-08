<?php

require_once('lib/bootstrap.php');

class FilesTest extends PHPUnit_Framework_TestCase {
  public function __construct() {
    $this->page  = site()->pages()->find('tests/files');   
    $this->files = $this->page->files(); 
  }
  
  public function testGlobalMethods() {
    $this->assertInstanceOf('Page', $this->files->page());
    $this->assertTrue($this->files->page()->equals($this->page));
  }
  
  public function testTypeFilters() {
    $this->assertInstanceOf('Files', $this->files->images());
    $this->assertTrue($this->files->hasImages());
    
    $this->assertInstanceOf('Files', $this->files->videos());
    $this->assertFalse($this->files->hasVideos());
    
    $this->assertInstanceOf('Files', $this->files->documents());
    $this->assertTrue($this->files->hasDocuments());
    
    $this->assertInstanceOf('Files', $this->files->sounds());
    $this->assertFalse($this->files->hasSounds());
    
    $this->assertInstanceOf('Files', $this->files->code());
    $this->assertTrue($this->files->hasCode());
    $this->assertEquals(2, $this->files->code()->count());
    
    $this->assertInstanceOf('Files', $this->files->metas());
    $this->assertTrue($this->files->hasMetas());
    $this->assertEquals(3, $this->files->metas()->count());
    
    $this->assertInstanceOf('Files', $this->files->contents());
    $this->assertTrue($this->files->hasContents());
    $this->assertEquals(1, $this->files->contents()->count());
  }

  public function testFilters() {
    $this->assertEquals(1, $this->files->code()->filterBy('extension', 'js')->count());
    $this->assertEquals(2, $this->files->code()->count());
    $this->assertEquals(1, $this->files->filterBy('extension', 'js')->count());
    $this->assertEquals(2, $this->files->filterBy('type', 'code')->count());
    $this->assertEquals(1, $this->files->filterBy('name', 'image-01')->count());
  }

  public function testFinders() {
    // find
    $this->assertEquals('content.txt', $this->files->find('content.txt')->filename());
    $this->assertEquals('css', $this->files->find('other-01.css')->extension());
    $this->assertNull($this->files->find('not-existing.css'));
    
    // multiple finds
    $this->assertInstanceOf('Files', $this->files->find('content.txt', 'image-01.jpg', 'other-01.css'));
    $this->assertEquals(3, $this->files->find('content.txt', 'image-01.jpg', 'other-01.css')->count());
    
    $this->assertEquals(5, $this->files->findBy('extension', array('txt', 'jpg'))->count());
    $this->assertEquals($this->files->findByExtension('txt', 'jpg'), $this->files->findBy('extension', array('txt', 'jpg')));
    
    $this->assertEquals(3, $this->files->findByType('meta')->count());    
  }
    
  public function testTraversing() {
    $this->assertEquals('content.txt', $this->files->first()->filename());
    $this->assertEquals('other-02.js', $this->files->last()->filename());
  }

  public function testSorting() {
    $files = $this->files->sortBy('filename', 'desc');
    
    $this->assertEquals($this->files->last()->filename(), $files->first()->filename());
    
    $files = $this->files->sortBy('extension', 'asc');
    
    $this->assertEquals('other-01.css', $files->first()->filename());
  }
}