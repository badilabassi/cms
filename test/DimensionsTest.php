<?php

require_once('testing_bootstrap.php');

class DimensionsTest extends PHPUnit_Framework_TestCase {
  public function __construct() {
    $this->dimensions = new KirbyDimensions(400, 500);
  }
  
  public function testInitializeDimensions() {
    $this->assertInstanceOf('KirbyDimensions', $this->dimensions);
  }

  public function testMethods() {
    $this->assertEquals(400, $this->dimensions->width());
    $this->assertEquals(500, $this->dimensions->height());
    $this->assertEquals(0.8, $this->dimensions->ratio());
    
    $image = clone $this->dimensions;
    $image = $image->fit(300);
    
    $this->assertEquals(240, $image->width());
    $this->assertEquals(300, $image->height());
    
    $image = clone $this->dimensions;
    $image = $image->fitWidth(200);
    
    $this->assertEquals(200, $image->width());
    $this->assertEquals(250, $image->height());
    
    $image = clone $this->dimensions;
    $image = $image->fitHeight(200);
    
    $this->assertEquals(160, $image->width());
    $this->assertEquals(200, $image->height());
  }
}