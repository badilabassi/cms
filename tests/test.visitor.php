<?php

require_once('bootstrap.php');

class TestOfVisitor extends UnitTestCase {

  function testInitializeVariable() {
    $this->visitor = site()->visitor();
  }

  function testMethods() {

    $this->assertTrue($this->visitor->ip() == '127.0.0.1');
    $this->assertTrue($this->visitor->language()->code() == 'en');

  }

}