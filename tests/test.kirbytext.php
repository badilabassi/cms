<?php

require_once('bootstrap.php');

class TestOfKirbytext extends UnitTestCase {

  function __construct() {
    $this->kt = new Kirbytext();  
    $this->url = 'http://superurl.com';
  }

  function testLink() {

    // a
    $link = $this->kt->tag('link', 'http://google.com'); 

    $expected = '<a href="http://google.com">http://google.com</a>';
    $this->assertTrue($link == $expected);

    // b
    $link = $this->kt->tag('link', 'http://google.com', array(
      'text' => 'Google'
    ));

    $expected = '<a href="http://google.com">Google</a>';
    $this->assertTrue($link == $expected);

    // c
    $link = $this->kt->tag('link', 'http://google.com', array(
      'text'   => 'Google',
      'title'  => 'Super title', 
      'class'  => 'classy', 
      'target' => '_parent',
    ));

    $expected = '<a href="http://google.com" class="classy" title="Super title" target="_parent">Google</a>';
    $this->assertTrue($link == $expected);

  }

  function testEmail() {

    /* not testable because of random encryption
    // email
    $email = $this->kt->email(array(
      'email' => 'mail@bastian-allgeier.de', 
      'text'  => 'Mail me'
    ));

    $expected = '<a href="http://google.com" class="classy" title="Super title" target="_parent">http://google.com</a>';
    $this->assertTrue($email == $expected);
    */

  }

  function testImage() {

    // a
    $image = $this->kt->tag('image', 'myimage.jpg');

    $expected = '<img src="' . $this->url . '/myimage.jpg" />';
    $this->assertTrue($image == $expected);

    // b
    $image = $this->kt->tag('image', 'myimage.jpg', array(
      'alt'    => 'My image'
    ));

    $expected = '<img src="' . $this->url . '/myimage.jpg" alt="My image" />';
    $this->assertTrue($image == $expected);

    // c
    $image = $this->kt->tag('image', 'myimage.jpg', array(
      'title'  => 'Super title', 
      'class'  => 'classy', 
    ));

    $expected = '<img src="' . $this->url . '/myimage.jpg" class="classy" title="Super title" />';
    $this->assertTrue($image == $expected);

    // d
    $image = $this->kt->tag('image', 'myimage.jpg', array(
      'title'  => 'Super title', 
      'class'  => 'classy', 
      'target' => '_parent',
      'link'   => 'http://google.com'
    ));

    $expected = '<a href="http://google.com" class="classy" title="Super title" target="_parent"><img src="' . $this->url . '/myimage.jpg" class="classy" title="Super title" /></a>';
    $this->assertTrue($image == $expected);

    // e
    $image = $this->kt->tag('image', 'myimage.jpg', array(
      'title'  => 'Super title', 
      'class'  => 'classy', 
      'target' => '_parent',
      'link'   => 'self'
    ));

    $expected = '<a href="' . $this->url . '/myimage.jpg" class="classy" title="Super title" target="_parent"><img src="' . $this->url . '/myimage.jpg" class="classy" title="Super title" /></a>';
    $this->assertTrue($image == $expected);

  }

  function testFile() {

    // a
    $file = $this->kt->tag('file', 'myfile.jpg');

    $expected = '<a href="' . $this->url . '/myfile.jpg">myfile.jpg</a>';
    $this->assertTrue($file == $expected);

    // b
    $file = $this->kt->tag('file', 'myfile.jpg', array(
      'text'   => 'What an awesome file'
    ));

    $expected = '<a href="' . $this->url . '/myfile.jpg">What an awesome file</a>';
    $this->assertTrue($file == $expected);

    // c
    $file = $this->kt->tag('file', 'myfile.jpg', array(
      'text'   => 'What an awesome file', 
      'title'  => 'Super title', 
      'class'  => 'classy', 
      'target' => '_parent'
    ));

    $expected = '<a href="' . $this->url . '/myfile.jpg" class="classy" title="Super title" target="_parent">What an awesome file</a>';
    $this->assertTrue($file == $expected);

  }

  function testTwitter() {

    // a
    $twitter = $this->kt->tag('twitter', 'bastianallgeier');

    $expected = '<a href="https://twitter.com/bastianallgeier">@bastianallgeier</a>';
    $this->assertTrue($twitter == $expected);

    // b
    $twitter = $this->kt->tag('twitter', 'bastianallgeier', array(
      'text' => 'This is my twitter account'
    ));

    $expected = '<a href="https://twitter.com/bastianallgeier">This is my twitter account</a>';
    $this->assertTrue($twitter == $expected);

    // c
    $twitter = $this->kt->tag('twitter', 'bastianallgeier', array(
      'text'    => 'This is my twitter account', 
      'target'  => '_blank'
    ));

    $expected = '<a href="https://twitter.com/bastianallgeier" target="_blank">This is my twitter account</a>';
    $this->assertTrue($twitter == $expected);

    // d
    $twitter = $this->kt->tag('twitter', 'bastianallgeier', array(
      'text'    => 'This is my twitter account', 
      'target'  => '_blank',
      'rel'     => 'Twitter'
    ));

    $expected = '<a href="https://twitter.com/bastianallgeier" rel="Twitter" target="_blank">This is my twitter account</a>';
    $this->assertTrue($twitter == $expected);

    // e
    $twitter = $this->kt->tag('twitter', 'bastianallgeier', array(
      'text'    => 'This is my twitter account', 
      'target'  => '_blank',
      'rel'     => 'Twitter', 
      'class'   => 'twitter',
    ));

    $expected = '<a href="https://twitter.com/bastianallgeier" class="twitter" rel="Twitter" target="_blank">This is my twitter account</a>';
    $this->assertTrue($twitter == $expected);

  }

  function testYoutube() {

    $youtube = $this->kt->tag('youtube', 'http://www.youtube.com/watch?feature=player_embedded&v=_9tHtxOCvy4');

    $expected = '<iframe src="http://www.youtube.com/embed/_9tHtxOCvy4" frameborder="0" webkitAllowFullScreen="true" mozAllowFullScreen="true" allowFullScreen="true" width="480" height="358"></iframe>';
    $this->assertTrue($youtube == $expected);

  }

  function testVimeo() {

    $vimeo = $this->kt->tag('vimeo', 'http://vimeo.com/52345557');

    $expected = '<iframe src="http://player.vimeo.com/video/52345557" frameborder="0" webkitAllowFullScreen="true" mozAllowFullScreen="true" allowFullScreen="true" width="480" height="358"></iframe>';
    $this->assertTrue($vimeo == $expected);

  }

  function testGist() {

    $gist = $this->kt->tag('gist', 'https://gist.github.com/2924148');

    $expected = '<script src="https://gist.github.com/2924148.js"></script>';
    $this->assertTrue($gist == $expected);

  }


}