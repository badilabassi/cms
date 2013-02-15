<?php

require_once('bootstrap.php');
require_once(c::get('root.lib') . DS . 'html' . DS . 'html.php');
require_once(c::get('root.lib') . DS . 'html' . DS . 'embed.php');
require_once(c::get('root.lib') . DS . 'html' . DS . 'form.php');

class TestOfHTML extends UnitTestCase {

  function testHTML() {

    $expected = '<img src="myimage.jpg" width="100" height="200" />';
    $this->assertTrue(html::tag('img', null, array('src' => 'myimage.jpg', 'width' => 100, 'height' => 200)) == $expected);
    
    $expected = '<a href="http://google.com" title="Google">Google</a>';
    $this->assertTrue(html::tag('a', 'Google', array('href' => 'http://google.com', 'title' => 'Google')) == $expected);

    $expected = '<p>Nice Paragraph</p>';
    $this->assertTrue(html::tag('p', 'Nice Paragraph') == $expected);
    
    $expected = '<br />';
    $this->assertTrue(html::tag('br') == $expected);

    $expected = '<a href="http://google.com" title="Google">Google</a>';
    $this->assertTrue(html::a('http://google.com', 'Google', array('title' => 'Google')) == $expected);

    $expected = '<img src="myimage.jpg" alt="myimage.jpg" width="100" height="200" />';
    $this->assertTrue(html::img('myimage.jpg', array('width' => 100, 'height' => 200)) == $expected);

    //dump(html::email('bastian@getkirby.com', 'Test'));

    $expected = '<p>Nice Paragraph</p>';
    $this->assertTrue(html::p('Nice Paragraph') == $expected);
    
    $expected = '<span>Nice Span</span>';
    $this->assertTrue(html::span('Nice Span') == $expected);

    $expected = '<link rel="stylesheet" href="screen.css" />';
    $this->assertTrue(html::stylesheet('screen.css') == $expected);

    $expected = '<link rel="stylesheet" href="screen.css" media="screen" />';
    $this->assertTrue(html::stylesheet('screen.css', 'screen') == $expected);

    $expected = '<script src="jquery.js"></script>';
    $this->assertTrue(html::script('jquery.js') == $expected);

    $expected = '<script src="jquery.js" async="async"></script>';
    $this->assertTrue(html::script('jquery.js', true) == $expected);

    $expected = '<link rel="shortcut icon" href="favicon.ico" />';
    $this->assertTrue(html::favicon('favicon.ico') == $expected);

    $expected = '<iframe src="http://google.com"></iframe>';
    $this->assertTrue(html::iframe('http://google.com') == $expected);

    $expected = '<!DOCTYPE html>';
    $this->assertTrue(html::doctype() == $expected);
 
    $expected = '<meta charset="utf-8" />';
    $this->assertTrue(html::charset() == $expected);

    $expected = '<link href="http://google.com" rel="canonical" />';
    $this->assertTrue(html::canonical('http://google.com') == $expected);
 
    $expected  = '<!--[if lt IE 9]>' . PHP_EOL;
    $expected .= '<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>' . PHP_EOL;
    $expected .= '<![endif]-->' . PHP_EOL;

    $this->assertTrue(html::shiv() == $expected);
 
    $expected = '<meta name="description" content="This is the description text for a website" />';
    $this->assertTrue(html::description('This is the description text for a website') == $expected);

    $expected = '<meta name="keywords" content="a, list, of, nice, keywords" />';
    $this->assertTrue(html::keywords('a, list, of, nice, keywords') == $expected);

  }

}