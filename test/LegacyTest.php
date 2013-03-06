<?php

require_once('lib/testing_bootstrap.php');

class LegacyTest extends PHPUnit_Framework_TestCase {

  public function testClasses() {

    $classes = array(
      'file',
      'files',
      'image',
      'obj',
      'page',
      'pages',
      'pagination',
      'tpl',
      'uri',
      'uriparams',
      'uripath',
      'uriquery',
      'variable',
      'video'
    );

    foreach($classes as $class) {
      $this->assertTrue(class_exists($class));
    }

  }

}