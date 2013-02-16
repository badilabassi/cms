<?php

require_once('bootstrap.php');

class TestOfFile extends UnitTestCase {

  function __construct() {
    $this->page = site()->pages()->find('tests/files');
  }

  function testDocument() {

    $root = $this->page->root() . '/document-01.pdf';
    $file = $this->page->files()->find('document-01.pdf');

    $this->assertIsA($file, 'KirbyFile');
    $this->assertTrue($file->root() == $root);
    $this->assertTrue($file->type() == 'document');
    $this->assertTrue($file->filename() == 'document-01.pdf');
    $this->assertTrue($file->dir() == dirname($root));
    $this->assertTrue($file->dirname() == 'files');
    $this->assertTrue($file->name() == 'document-01');
    $this->assertTrue($file->extension() == 'pdf');
    $this->assertTrue($file->size() == 4944);
    $this->assertTrue($file->niceSize() == '4.83 kb');
    $this->assertTrue($file->mime() == 'application/pdf');

  }

  function testImage() {

    $root = $this->page->root() . '/image-01.jpg';
    $file = $this->page->files()->find('image-01.jpg');

    $this->assertIsA($file, 'KirbyFile');
    $this->assertTrue($file->root() == $root);
    $this->assertTrue($file->type() == 'image');
    $this->assertTrue($file->filename() == 'image-01.jpg');
    $this->assertTrue($file->dir() == dirname($root));
    $this->assertTrue($file->dirname() == 'files');
    $this->assertTrue($file->name() == 'image-01');
    $this->assertTrue($file->extension() == 'jpg');
    $this->assertTrue($file->size() == 433);
    $this->assertTrue($file->niceSize() == '433 b');
    $this->assertTrue($file->mime() == 'image/jpeg');

    $this->assertIsA($file, 'KirbyImage');
    $this->assertIsA($file->dimensions(), 'KirbyDimensions');
    $this->assertTrue($file->mime(), 'image/jpeg');
    $this->assertTrue($file->width() == 100);
    $this->assertTrue($file->height() == 100);

    // test meta information of image files
    $this->assertIsA($file->meta(), 'KirbyContent');
    $this->assertTrue($file->hasMeta());
    $this->assertIsA($file->title(), 'KirbyVariable');
    $this->assertTrue($file->title() == 'Title for image-01');
    $this->assertTrue($file->caption() == 'Caption for image-01');
    $this->assertTrue($file->meta()->fields() == array('title', 'caption'));
    $this->assertTrue($file->meta()->raw() == f::read($file->meta()->root()));

  }

  function testContent() {

    $root = $this->page->root() . '/content.txt';
    $file = $this->page->files()->find('content.txt');

    $this->assertIsA($file, 'KirbyFile');
    $this->assertTrue($file->root() == $root);
    $this->assertTrue($file->type() == 'content');
    $this->assertTrue($file->filename() == 'content.txt');
    $this->assertTrue($file->dir() == dirname($root));
    $this->assertTrue($file->dirname() == 'files');
    $this->assertTrue($file->name() == 'content');
    $this->assertTrue($file->extension() == 'txt');
    $this->assertTrue($file->size() == 61);
    $this->assertTrue($file->niceSize() == '61 b');
    $this->assertTrue($file->mime() == 'text/plain');

    $this->assertIsA($file, 'KirbyContent');
    $this->assertTrue($file->raw() == f::read($file->root()));
    $this->assertTrue(is_array($file->data()));
    $this->assertIsA($file->title(), 'KirbyVariable');
    $this->assertTrue($file->title() == 'My content file');
    $this->assertTrue($file->fields() == array('title', 'text'));

  }

}