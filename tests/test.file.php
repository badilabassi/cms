<?php

require_once('bootstrap.php');
require_once(c::get('root.lib') . DS . 'page.php');

class TestOfFile extends UnitTestCase {

  function __construct() {
    $this->filesdir = TEST_CONTENT . '/01-tests/files';
  }

  function testDocument() {

    $root = $this->filesdir . '/document-01.pdf';
    $file = new KirbyFile($root);

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

    $root = $this->filesdir . '/image-01.jpg';
    $file = new KirbyFile($root);

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

    $image = new KirbyImage($file);

    $this->assertIsA($image, 'KirbyImage');
    $this->assertIsA($image->dimensions(), 'KirbyDimensions');
    $this->assertTrue($image->mime(), 'image/jpeg');
    $this->assertTrue($image->width() == 100);
    $this->assertTrue($image->height() == 100);

  }

  function testContent() {

    $root = $this->filesdir . '/content.txt';
    $file = new KirbyFile($root);

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

    $content = new KirbyContent($file);

    $this->assertIsA($content, 'KirbyContent');
    $this->assertTrue($content->raw() == f::read($content->root()));
    $this->assertTrue(is_array($content->data()));
    $this->assertIsA($content->title(), 'KirbyVariable');
    $this->assertTrue($content->title() == 'My content file');
    $this->assertTrue($content->fields() == array('title', 'text'));

  }

}