<?php

require_once('bootstrap.php');

class TestOfVariable extends UnitTestCase {

  function testInitializeVariable() {
    $this->var = new KirbyVariable('title', 'This is a test title');
  }

  function testMethods() {

    $this->assertTrue($this->var->key() == 'title');
    $this->assertTrue($this->var->value() == 'This is a test title');
    $this->assertTrue((string)$this->var == 'This is a test title');

    $this->var = new KirbyVariable('title', '');

    $this->assertTrue($this->var->isEmpty());

  }

}