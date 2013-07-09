<?php

require_once('lib/bootstrap.php');

class VisitorTest extends PHPUnit_Framework_TestCase {
  public function __construct() {
    $this->visitor = site()->visitor();
  }
  
  public function testMethods() {
    $this->assertEquals('0.0.0.0', $this->visitor->ip());
    $this->assertFalse($this->visitor->language());
  
    site(array(
      'lang.support' => true,
      'lang.current' => 'en'
    ));

    $this->assertEquals('en', $this->visitor->language()->code());

    site(array(
      'lang.support' => false,
    ));
  
  }
}