<?php

require_once('lib/testing_bootstrap.php');

class VariableTest extends PHPUnit_Framework_TestCase {
  public function __construct() {
    $this->var = new Variable('title', 'This is a test title');
  }
  
  public function testMethods() {
    $this->assertEquals('title', $this->var->key());
    $this->assertEquals('This is a test title', $this->var->value());
    $this->assertEquals('This is a test title', (string)$this->var);
    
    $this->var = new Variable('title', '');
    
    $this->assertTrue($this->var->isEmpty());
  }
}