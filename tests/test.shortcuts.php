<?php

require_once('bootstrap.php');

class TestOfShortcuts extends UnitTestCase {

  function __construct() {
    $this->url = 'http://superurl.com';
  }

  function testKirbytags() {

    c::set('lang.support', false);

    // link a
    $text     = '(link: http://google.com text: Google)';
    $result   = kirbytext($text, false);
    $expected = '<a href="http://google.com">Google</a>';
    //dump($result);

    $this->assertTrue($result == $expected);

    // link b
    $text     = '(link: http://google.com text: Google title: Google class: google rel: google)';
    $result   = kirbytext($text, false);
    $expected = '<a href="http://google.com" rel="google" class="google" title="Google">Google</a>';
    //dump($result);

    $this->assertTrue($result == $expected);

    // link c
    $text     = '(link: http://google.com text: Google title: Google class: google rel: google popup: true)';
    $result   = kirbytext($text, false);
    $expected = '<a href="http://google.com" rel="google" class="google" title="Google" target="_blank">Google</a>';
    //dump($result);

    $this->assertTrue($result == $expected);

    // link d
    $text     = '(link: http://google.com text: Google title: Google class: google rel: google target: _parent)';
    $result   = kirbytext($text, false);
    $expected = '<a href="http://google.com" rel="google" class="google" title="Google" target="_parent">Google</a>';
    //dump($result);

    $this->assertTrue($result == $expected);

    // link e
    $text     = '(link: http://google.com title: Google class: google rel: google target: _parent)';
    $result   = kirbytext($text, false);
    $expected = '<a href="http://google.com" rel="google" class="google" title="Google" target="_parent">http://google.com</a>';
    //dump($result);

    $this->assertTrue($result == $expected);

    // image a
    $text     = '(image: myimage.jpg)';
    $result   = kirbytext($text, false);
    $expected = '<img src="' . $this->url . '/myimage.jpg" />';
    // dump($result);

    $this->assertTrue($result == $expected);

    // image b
    $text     = '(image: myimage.jpg alt: My Image)';
    $result   = kirbytext($text, false);
    $expected = '<img src="' . $this->url . '/myimage.jpg" alt="My Image" />';
    //dump($result);

    $this->assertTrue($result == $expected);

    // image c
    $text     = '(image: myimage.jpg text: My Image)';
    $result   = kirbytext($text, false);
    $expected = '<img src="' . $this->url . '/myimage.jpg" alt="My Image" />';
    //dump($result);

    $this->assertTrue($result == $expected);

    // image d
    $text     = '(image: myimage.jpg text: My Image title: My Title)';
    $result   = kirbytext($text, false);
    $expected = '<img src="' . $this->url . '/myimage.jpg" alt="My Image" title="My Title" />';
    //dump($result);

    $this->assertTrue($result == $expected);

    // image e
    $text     = '(image: myimage.jpg text: My Image title: My Title class: test-class)';
    $result   = kirbytext($text, false);
    $expected = '<img src="' . $this->url . '/myimage.jpg" alt="My Image" class="test-class" title="My Title" />';
    //dump($result);

    $this->assertTrue($result == $expected);

    // image f
    $text     = '(image: myimage.jpg link: http://google.com)';
    $result   = kirbytext($text, false);
    $expected = '<a href="http://google.com"><img src="' . $this->url . '/myimage.jpg" /></a>';
    //dump($result);

    $this->assertTrue($result == $expected);

    // image g
    $text     = '(image: myimage.jpg link: self)';
    $result   = kirbytext($text, false);
    $expected = '<a href="' . $this->url . '/myimage.jpg"><img src="' . $this->url . '/myimage.jpg" /></a>';
    //dump($result);

    $this->assertTrue($result == $expected);

  }

}