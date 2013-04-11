<?php

require_once('lib/testing_bootstrap.php');

class VisitorTest extends PHPUnit_Framework_TestCase {
  public function __construct() {
    $this->visitor = site()->visitor();
  }
  
  public function testMethods() {
    $this->assertEquals('0.0.0.0', $this->visitor->ip());
    $this->assertEquals('en', $this->visitor->language()->code());
  }
}