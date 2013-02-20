<?php

require_once('testing_bootstrap.php');

class VisitorTest extends PHPUnit_Framework_TestCase {
  public function __construct() {
    $this->visitor = site()->visitor();
  }
  
  public function testMethods() {
    $this->assertEquals('127.0.0.1', $this->visitor->ip());
    $this->assertEquals('en', $this->visitor->language()->code());
  }
}