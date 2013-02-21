<?php

require_once('testing_bootstrap.php');

class HelpersTest extends PHPUnit_Framework_TestCase {
  function __construct() {
    $this->url = 'http://superurl.com';
  }
  
  public function testURL() {
    // switch back to activated rewriting
    site(array(
      'url'       => $this->url, 
      'subfolder' => ''
    ));
    
    $this->assertEquals($this->url, url());
    $this->assertEquals($this->url . '/super/nice', url('super/nice'));
    $this->assertEquals($this->url . '/super/nice', url('/super/nice'));
    $this->assertEquals($this->url . '/super/nice', url('super/nice/'));
    $this->assertEquals($this->url . '/super/nice', url('/super/nice/'));
    $this->assertEquals($this->url . '/super/nice', url('//super/nice///'));
    $this->assertEquals($this->url . '/index.php', url('index.php'));
    $this->assertEquals('http://jquery.com', url('http://jquery.com'));
    
    site(array(
      'url'       => $this->url, 
      'rewrite'   => false, 
      'subfolder' => ''
    ));
    
    $this->assertEquals($this->url . '/index.php/super/nice', url('super/nice'));
    $this->assertEquals($this->url . '/index.php/super/nice', url('/super/nice'));
    $this->assertEquals($this->url . '/index.php/super/nice', url('/super/nice/'));
    $this->assertEquals($this->url . '/index.php/super/nice', url('//super/nice///'));
    $this->assertEquals($this->url, url());
    
    $this->assertTrue(url('super/nice', 'de', array(
      'param1' => 'value1',
      'param2' => 'value2'
    ), array(
      'var1' => 'value1',
      'var2' => 'value2'
    )) == 'http://superurl.com/index.php/super/nice/param1:value1/param2:value2?var1=value1&var2=value2');
    
    // TODO: check url with language parameter
    
    $this->assertEquals(u(), url());
    
    // not testable
    // $this->assertEquals($this->url, thisURL());
  }
  
  public function testOtherHelpers() {
    // switch back to activated rewriting
    site(array(
      'url'       => $this->url, 
      'subfolder' => '', 
      'rewrite'   => true
    ));
    
    $expected = '<link rel="stylesheet" href="' . $this->url . '/assets/css/screen.css" />' . "\n";
    $this->assertEquals(css('assets/css/screen.css'), $expected);
    
    $expected = '<link rel="stylesheet" media="screen" href="' . $this->url . '/assets/css/screen.css" />' . "\n";
    $this->assertEquals(css('assets/css/screen.css', 'screen'), $expected);
    
    $expected = '<script src="' . $this->url . '/assets/js/jquery.js"></script>' . "\n";
    $this->assertEquals(js('assets/js/jquery.js'), $expected);
    
    $expected = '<script async src="' . $this->url . '/assets/js/jquery.js"></script>' . "\n";
    $this->assertEquals(js('assets/js/jquery.js', true), $expected);
  }
  
  public function testKirbytags() {
    c::set('lang.support', false);
    
    // link a
    $text     = '(link: http://google.com text: Google)';
    $result   = kirbytext($text, false);
    $expected = '<a href="http://google.com">Google</a>';
    
    $this->assertEquals($expected, $result);
    
    // link b
    $text     = '(link: http://google.com text: Google title: Google class: google rel: google)';
    $result   = kirbytext($text, false);
    $expected = '<a href="http://google.com" rel="google" class="google" title="Google">Google</a>';
    
    $this->assertEquals($expected, $result);
    
    // link c
    $text     = '(link: http://google.com text: Google title: Google class: google rel: google popup: true)';
    $result   = kirbytext($text, false);
    $expected = '<a href="http://google.com" rel="google" class="google" title="Google" target="_blank">Google</a>';
    
    $this->assertEquals($expected, $result);
    
    // link d
    $text     = '(link: http://google.com text: Google title: Google class: google rel: google target: _parent)';
    $result   = kirbytext($text, false);
    $expected = '<a href="http://google.com" rel="google" class="google" title="Google" target="_parent">Google</a>';
    
    $this->assertEquals($expected, $result);
    
    // link e
    $text     = '(link: http://google.com title: Google class: google rel: google target: _parent)';
    $result   = kirbytext($text, false);
    $expected = '<a href="http://google.com" rel="google" class="google" title="Google" target="_parent">http://google.com</a>';
    
    $this->assertEquals($expected, $result);
    
    // image a
    $text     = '(image: myimage.jpg)';
    $result   = kirbytext($text, false);
    $expected = '<img src="' . $this->url . '/myimage.jpg" />';
    
    $this->assertEquals($expected, $result);
    
    // image b
    $text     = '(image: myimage.jpg alt: My Image)';
    $result   = kirbytext($text, false);
    $expected = '<img src="' . $this->url . '/myimage.jpg" alt="My Image" />';
    
    $this->assertEquals($expected, $result);
    
    // image c
    $text     = '(image: myimage.jpg text: My Image)';
    $result   = kirbytext($text, false);
    $expected = '<img src="' . $this->url . '/myimage.jpg" alt="My Image" />';
    
    $this->assertEquals($expected, $result);
    
    // image d
    $text     = '(image: myimage.jpg text: My Image title: My Title)';
    $result   = kirbytext($text, false);
    $expected = '<img src="' . $this->url . '/myimage.jpg" alt="My Image" title="My Title" />';
    
    $this->assertEquals($expected, $result);
    
    // image e
    $text     = '(image: myimage.jpg text: My Image title: My Title class: test-class)';
    $result   = kirbytext($text, false);
    $expected = '<img src="' . $this->url . '/myimage.jpg" alt="My Image" class="test-class" title="My Title" />';
    
    $this->assertEquals($expected, $result);
    
    // image f
    $text     = '(image: myimage.jpg link: http://google.com)';
    $result   = kirbytext($text, false);
    $expected = '<a href="http://google.com"><img src="' . $this->url . '/myimage.jpg" /></a>';
    
    $this->assertEquals($expected, $result);
    
    // image g
    $text     = '(image: myimage.jpg link: self)';
    $result   = kirbytext($text, false);
    $expected = '<a href="' . $this->url . '/myimage.jpg"><img src="' . $this->url . '/myimage.jpg" /></a>';
    
    $this->assertEquals($expected, $result);
  }
}