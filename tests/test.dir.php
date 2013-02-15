<?php

require_once('bootstrap.php');

class TestOfDir extends UnitTestCase {

  function testMainDir() {

    $root = TEST_CONTENT;
    $dir  = new KirbyDir($root);

    $this->assertTrue($dir->root() == $root);
    $this->assertTrue($dir->name() == basename(TEST_CONTENT));
    $this->assertTrue($dir->uid() == basename(TEST_CONTENT));
    $this->assertTrue($dir->num() == '');
    $this->assertTrue($dir->modified() == filectime($root));
    $this->assertTrue($dir->isReadable() == is_readable($root));
    $this->assertTrue($dir->isWritable() == is_writable($root));

    // the main test dir is located in @root/kirby/tests/content
    $this->assertTrue($dir->uri() == basename(TEST_KIRBY_CORE) . DS . 'tests' . DS . 'content');

    // echoing the test dir should result in the root 
    $this->assertTrue((string)$dir == $root);

    // the main test dir should only contain the site.txt
    $this->assertTrue(count($dir->files()) == 1);

    // the main test dir should contain three folders: error, home and 01-tests
    $this->assertTrue(count($dir->children()) == 3);

    $this->assertTrue(is_array($dir->files()));
    $this->assertTrue(is_array($dir->children()));

  }

  function testTestsDir() {

    $root = TEST_CONTENT . '/01-tests';
    $dir  = new KirbyDir($root);

    $this->assertTrue($dir->name() == '01-tests');
    $this->assertTrue($dir->uid() == 'tests');
    $this->assertTrue($dir->num() == '01');

     // the tests dir is located in @root/kirby/tests/content/01-tests
    $this->assertTrue($dir->uri() == basename(TEST_KIRBY_CORE) . DS . 'tests' . DS . 'content' . DS . '01-tests');

  }

}