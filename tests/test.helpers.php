<?php

require_once('bootstrap.php');

class TestOfHelpers extends UnitTestCase {

  function __construct() {
    $this->url = 'http://superurl.com';
  }

  function testURL() {

    // switch back to activated rewriting
    site(array(
      'url'       => $this->url, 
      'subfolder' => ''
    ));

    $this->assertTrue(url(), $this->url);
    $this->assertTrue(url('super/nice') == $this->url . '/super/nice');
    $this->assertTrue(url('/super/nice') == $this->url . '/super/nice');
    $this->assertTrue(url('super/nice/') == $this->url . '/super/nice');
    $this->assertTrue(url('/super/nice/') == $this->url . '/super/nice');
    $this->assertTrue(url('//super/nice///') == $this->url . '/super/nice');
    $this->assertTrue(url('index.php') == $this->url . '/index.php');
    $this->assertTrue(url('http://jquery.com') == 'http://jquery.com');

    site(array(
      'url'       => $this->url, 
      'rewrite'   => false, 
      'subfolder' => ''
    ));

    $this->assertTrue(url('super/nice') == $this->url . '/index.php/super/nice');
    $this->assertTrue(url('/super/nice') == $this->url . '/index.php/super/nice');
    $this->assertTrue(url('/super/nice/') == $this->url . '/index.php/super/nice');
    $this->assertTrue(url('//super/nice///') == $this->url . '/index.php/super/nice');
    $this->assertTrue(url() == $this->url);

    $this->assertTrue(url('super/nice', 'de', array(
      'param1' => 'value1',
      'param2' => 'value2'
    ), array(
      'var1' => 'value1',
      'var2' => 'value2'
    )) == 'http://superurl.com/index.php/super/nice/param1:value1/param2:value2?var1=value1&var2=value2');

    // TODO: check url with language parameter

    $this->assertTrue(url() == u());
    
    // not testable
    // $this->assertTrue(thisURL() == $this->url);

  }

  function testOtherHelpers() {

    // switch back to activated rewriting
    site(array(
      'url'       => $this->url, 
      'subfolder' => '', 
      'rewrite'   => true
    ));

    $expected = '<link rel="stylesheet" href="' . $this->url . '/assets/css/screen.css" />' . "\n";
    $this->assertTrue($expected == css('assets/css/screen.css'));

    $expected = '<link rel="stylesheet" media="screen" href="' . $this->url . '/assets/css/screen.css" />' . "\n";
    $this->assertTrue($expected == css('assets/css/screen.css', 'screen'));

    $expected = '<script src="' . $this->url . '/assets/js/jquery.js"></script>' . "\n";
    $this->assertTrue($expected == js('assets/js/jquery.js'));
    
    $expected = '<script async src="' . $this->url . '/assets/js/jquery.js"></script>' . "\n";
    $this->assertTrue($expected == js('assets/js/jquery.js', true));

  }

}