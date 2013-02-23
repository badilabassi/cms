<?php

require_once('lib/testing_bootstrap.php');
require_once(TEST_KIRBY_LIB . DS . 'html' . DS . 'html.php');
require_once(TEST_KIRBY_LIB . DS . 'html' . DS . 'embed.php');
require_once(TEST_KIRBY_LIB . DS . 'html' . DS . 'form.php');

class HTMLTest extends PHPUnit_Framework_TestCase {
  public function testHTML() {
    $expected = '<img src="myimage.jpg" width="100" height="200" />';
    $this->assertEquals($expected, html::tag('img', null, array('src' => 'myimage.jpg', 'width' => 100, 'height' => 200)));
    
    $expected = '<a href="http://google.com" title="Google">Google</a>';
    $this->assertEquals($expected, html::tag('a', 'Google', array('href' => 'http://google.com', 'title' => 'Google')));
    
    $expected = '<p>Nice Paragraph</p>';
    $this->assertEquals($expected, html::tag('p', 'Nice Paragraph'));
    
    $expected = '<br />';
    $this->assertEquals($expected, html::tag('br'));
    
    $expected = '<a href="http://google.com" title="Google">Google</a>';
    $this->assertEquals($expected, html::a('http://google.com', 'Google', array('title' => 'Google')));
    
    $expected = '<img src="myimage.jpg" alt="myimage.jpg" width="100" height="200" />';
    $this->assertEquals($expected, html::img('myimage.jpg', array('width' => 100, 'height' => 200)));
    
    $expected = '<p>Nice Paragraph</p>';
    $this->assertEquals($expected, html::p('Nice Paragraph'));
    
    $expected = '<span>Nice Span</span>';
    $this->assertEquals($expected, html::span('Nice Span'));
    
    $expected = '<link rel="stylesheet" href="screen.css" />';
    $this->assertEquals($expected, html::stylesheet('screen.css'));
    
    $expected = '<link rel="stylesheet" href="screen.css" media="screen" />';
    $this->assertEquals($expected, html::stylesheet('screen.css', 'screen'));
    
    $expected = '<script src="jquery.js"></script>';
    $this->assertEquals($expected, html::script('jquery.js'));
    
    $expected = '<script src="jquery.js" async="async"></script>';
    $this->assertEquals($expected, html::script('jquery.js', true));
    
    $expected = '<link rel="shortcut icon" href="favicon.ico" />';
    $this->assertEquals($expected, html::favicon('favicon.ico'));
    
    $expected = '<iframe src="http://google.com"></iframe>';
    $this->assertEquals($expected, html::iframe('http://google.com'));
    
    $expected = '<!DOCTYPE html>';
    $this->assertEquals($expected, html::doctype());
    
    $expected = '<meta charset="utf-8" />';
    $this->assertEquals($expected, html::charset());
    
    $expected = '<link href="http://google.com" rel="canonical" />';
    $this->assertEquals($expected, html::canonical('http://google.com'));
    
    $expected  = '<!--[if lt IE 9]>' . PHP_EOL;
    $expected .= '<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>' . PHP_EOL;
    $expected .= '<![endif]-->' . PHP_EOL;
    
    $this->assertEquals($expected, html::shiv());
    
    $expected = '<meta name="description" content="This is the description text for a website" />';
    $this->assertEquals($expected, html::description('This is the description text for a website'));
    
    $expected = '<meta name="keywords" content="a, list, of, nice, keywords" />';
    $this->assertEquals($expected, html::keywords('a, list, of, nice, keywords'));
  }
}