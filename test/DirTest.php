<?php

require_once('lib/bootstrap.php');

class DirTest extends PHPUnit_Framework_TestCase {
  public function testMainDir() {
    $root = TEST_CONTENT;
    $dir  = new PageDir($root);

    $this->assertEquals($root, $dir->root());
    $this->assertEquals(basename(TEST_CONTENT), $dir->name());
    $this->assertEquals(basename(TEST_CONTENT), $dir->uid());
    $this->assertEquals('', $dir->num());
    $this->assertEquals(filectime($root), $dir->modified());
    $this->assertEquals(is_readable($root), $dir->isReadable());
    $this->assertEquals(is_writable($root), $dir->isWritable());

    // the main test dir is located in @root/test/testContent
    $this->assertEquals(basename(TEST_KIRBY_CORE) . DS . 'test' . DS . 'etc' . DS . basename(TEST_CONTENT), $dir->uri());

    // echoing the test dir should result in the root 
    $this->assertEquals($root, (string)$dir);

    // the main test dir should only contain the site.txt
    $this->assertEquals(1, count($dir->files()));

    // the main test dir should contain three folders: error, home and 01-tests
    $this->assertEquals(3, count($dir->children()));

    $this->assertTrue(is_array($dir->files()));
    $this->assertTrue(is_array($dir->children()));
  }

  public function testTestsDir() {
    $root = TEST_CONTENT . '/01-tests';
    $dir  = new PageDir($root);

    $this->assertEquals('01-tests', $dir->name());
    $this->assertEquals('tests', $dir->uid());
    $this->assertEquals('01', $dir->num());

    // the tests dir is located in @root/test/testContent/01-tests
    $this->assertEquals(basename(TEST_KIRBY_CORE) . DS . 'test' . DS . 'etc' . DS . basename(TEST_CONTENT) . DS . '01-tests', $dir->uri());
  }
}