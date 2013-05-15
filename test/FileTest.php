<?php

require_once('lib/bootstrap.php');

class FileTest extends PHPUnit_Framework_TestCase {
  public function __construct() {
    $this->page = site()->pages()->find('tests/files');
  }
  
  public function testDocument() {
    $root = $this->page->root() . '/document-01.pdf';
    $file = $this->page->files()->find('document-01.pdf');
    
    $this->assertInstanceOf('File', $file);
    $this->assertEquals($root, $file->root());
    $this->assertEquals('document', $file->type());
    $this->assertEquals('document-01.pdf', $file->filename());
    $this->assertEquals(dirname($root), $file->dir());
    $this->assertEquals('files', $file->dirname());
    $this->assertEquals('document-01', $file->name());
    $this->assertEquals('pdf', $file->extension());
    $this->assertEquals(4944, $file->size());
    $this->assertEquals('4.83 kb', $file->niceSize());
    $this->assertEquals('application/pdf', $file->mime());
  }
  
  public function testImage() {
    $root = $this->page->root() . '/image-01.jpg';
    $file = $this->page->files()->find('image-01.jpg');
    
    $this->assertInstanceOf('File', $file);
    $this->assertEquals($root, $file->root());
    $this->assertEquals('image', $file->type());
    $this->assertEquals('image-01.jpg', $file->filename());
    $this->assertEquals(dirname($root), $file->dir());
    $this->assertEquals('files', $file->dirname());
    $this->assertEquals('image-01', $file->name());
    $this->assertEquals('jpg', $file->extension());
    $this->assertEquals(433, $file->size());
    $this->assertEquals('433 b', $file->niceSize());
    $this->assertEquals('image/jpeg', $file->mime());
    
    $this->assertInstanceOf('ImageFile', $file);
    $this->assertInstanceOf('Dimensions', $file->dimensions());
    $this->assertEquals('image/jpeg', $file->mime());
    $this->assertEquals(100, $file->width());
    $this->assertEquals(100, $file->height());
    
    // test meta information of image files
    $this->assertInstanceOf('ContentFile', $file->meta());
    $this->assertTrue($file->hasMeta());
    $this->assertInstanceOf('Variable', $file->title());
    $this->assertEquals('Title for image-01', $file->title());
    $this->assertEquals('Caption for image-01', $file->caption());
    $this->assertEquals(array('title', 'caption'), $file->meta()->fields());
    $this->assertEquals(f::read($file->meta()->root()), $file->meta()->raw());
    
    // test attached thumbs
    $p = site()->pages()->find('tests/thumb');
    $image = $p->images()->find('image-01.jpg');
    
    $this->assertInstanceOf('ImageFile', $image->thumb());
    $this->assertEquals('image-01.thumb.jpg', $image->thumb()->filename());
    $this->assertTrue($image->hasThumb());
  }
  
  public function testContent() {
    
    $root = $this->page->root() . '/content.txt';
    $file = $this->page->files()->find('content.txt');
    
    $this->assertInstanceOf('File', $file);
    $this->assertEquals($root, $file->root());
    $this->assertEquals('content', $file->type());
    $this->assertEquals('content.txt', $file->filename());
    $this->assertEquals(dirname($root), $file->dir());
    $this->assertEquals('files', $file->dirname());
    $this->assertEquals('content', $file->name());
    $this->assertEquals('txt', $file->extension());
    $this->assertEquals(61, $file->size());
    $this->assertEquals('61 b', $file->niceSize());
    $this->assertEquals('text/plain', $file->mime());
    
    $this->assertInstanceOf('ContentFile', $file);
    $this->assertEquals(f::read($file->root()), $file->raw());
    $this->assertTrue(is_array($file->data()));
    $this->assertInstanceOf('Variable', $file->title());
    $this->assertEquals('My content file', $file->title());
    $this->assertEquals(array('title', 'text'), $file->fields());
  }
}