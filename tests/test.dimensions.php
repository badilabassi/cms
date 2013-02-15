<?php

require_once('bootstrap.php');

class TestOfDimensions extends UnitTestCase {

  function testInitializeDimensions() {
    $this->dimensions = new KirbyDimensions(400, 500);
  }

  function testMethods() {

    $this->assertTrue($this->dimensions->width() == 400);
    $this->assertTrue($this->dimensions->height() == 500);
    $this->assertTrue($this->dimensions->ratio() == 0.8);

    $image = clone $this->dimensions;
    $image = $image->fit(300);

    $this->assertTrue($image->width() == 240);
    $this->assertTrue($image->height() == 300);

    $image = clone $this->dimensions;
    $image = $image->fitWidth(200);

    $this->assertTrue($image->width() == 200);
    $this->assertTrue($image->height() == 250);

    $image = clone $this->dimensions;
    $image = $image->fitHeight(200);

    $this->assertTrue($image->width() == 160);
    $this->assertTrue($image->height() == 200);
    
  }

}