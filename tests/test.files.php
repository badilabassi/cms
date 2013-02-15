<?php

require_once('bootstrap.php');

class TestOfFiles extends UnitTestCase {

  function __construct() {
    
    $this->page  = new KirbyPage(TEST_CONTENT . '/01-tests/files');
    $this->files = $this->page->files(); 

  }

  function testGlobalMethods() {

    $this->assertIsA($this->files->page(), 'KirbyPage');
    $this->assertTrue($this->files->page()->equals($this->page));

  }

  function testTypeFilters() {

    $this->assertIsA($this->files->images(), 'KirbyFiles');
    $this->assertTrue($this->files->hasImages());

    $this->assertIsA($this->files->videos(), 'KirbyFiles');
    $this->assertFalse($this->files->hasVideos());

    $this->assertIsA($this->files->documents(), 'KirbyFiles');
    $this->assertTrue($this->files->hasDocuments());

    $this->assertIsA($this->files->sounds(), 'KirbyFiles');
    $this->assertFalse($this->files->hasSounds());

    $this->assertIsA($this->files->others(), 'KirbyFiles');
    $this->assertTrue($this->files->hasOthers());
    $this->assertTrue($this->files->others()->count() == 2);

    $this->assertIsA($this->files->metas(), 'KirbyFiles');
    $this->assertTrue($this->files->hasMetas());
    $this->assertTrue($this->files->metas()->count() == 3);

    $this->assertIsA($this->files->contents(), 'KirbyFiles');
    $this->assertTrue($this->files->hasContents());
    $this->assertTrue($this->files->contents()->count() == 1);

  }

  function testFilters() {

    $this->assertTrue($this->files->others()->filterBy('extension', 'js')->count() == 1);
    $this->assertTrue($this->files->others()->count() == 2);
    $this->assertTrue($this->files->filterBy('extension', 'js')->count() == 1);
    $this->assertTrue($this->files->filterBy('type', 'other')->count() == 2);
    $this->assertTrue($this->files->filterBy('name', 'image-01')->count() == 1);
    
  }

  function testFinders() {

    // find
    $this->assertTrue($this->files->find('content.txt')->filename() == 'content.txt');
    $this->assertTrue($this->files->find('other-01.css')->extension() == 'css');
    $this->assertTrue($this->files->find('not-existing.css') == null);

    // multiple finds
    $this->assertIsA($this->files->find('content.txt', 'image-01.jpg', 'other-01.css'), 'KirbyFiles');
    $this->assertTrue($this->files->find('content.txt', 'image-01.jpg', 'other-01.css')->count() == 3);

    $this->assertTrue($this->files->findBy('extension', array('txt', 'jpg'))->count() == 5);
    $this->assertTrue($this->files->findBy('extension', array('txt', 'jpg')) == $this->files->findByExtension('txt', 'jpg'));

    $this->assertTrue($this->files->findByType('meta')->count() == 3);    

  }
    
  function testTraversing() {

    $this->assertTrue($this->files->first()->filename() == 'content.txt');
    $this->assertTrue($this->files->last()->filename() == 'other-02.js');

  }

  function testSorting() {

    $files = $this->files->sortBy('filename', 'desc');

    $this->assertTrue($files->first()->filename() == $this->files->last()->filename());

    $files = $this->files->sortBy('extension', 'asc');

    $this->assertTrue($files->first()->filename() == 'other-01.css');

  }

}