<?php

require_once('lib/testing_bootstrap.php');

class KirbytextTest extends PHPUnit_Framework_TestCase {
  public function __construct() {
    $this->kt = new Kirbytext();  
    $this->url = 'http://superurl.com';
  }
  
  public function testLink() {
    c::set('markdown', false);
    
    // a
    $link = $this->kt->tag('link', 'http://google.com'); 
    $tag  = kirbytext('(link: http://google.com)');
    
    $expected = '<a href="http://google.com">http://google.com</a>';
    $this->assertEquals($expected, $link);
    $this->assertEquals($expected, $tag);
    
    // b
    $link = $this->kt->tag('link', 'http://google.com', array(
      'text' => 'Google'
    ));
    
    $tag = kirbytext('(link: http://google.com text: Google)');
    
    $expected = '<a href="http://google.com">Google</a>';
    $this->assertEquals($expected, $link);
    $this->assertEquals($expected, $tag);
    
    // c
    $link = $this->kt->tag('link', 'http://google.com', array(
      'text'   => 'Google',
      'title'  => 'Super title', 
      'class'  => 'classy', 
      'target' => '_parent',
    ));
    
    $tag = kirbytext('(link: http://google.com text: Google title: Super title class: classy target: _parent)');
    
    $expected = '<a href="http://google.com" class="classy" title="Super title" target="_parent">Google</a>';
    $this->assertEquals($expected, $link);
    $this->assertEquals($expected, $tag);
  }
  
  public function testEmail() {
    /* not testable because of random encryption
    // email
    $email = $this->kt->email(array(
      'email' => 'mail@bastian-allgeier.de', 
      'text'  => 'Mail me'
    ));
    
    $expected = '<a href="http://google.com" class="classy" title="Super title" target="_parent">http://google.com</a>';
    $this->assertEquals($expected, $email);
    */
  }
  
  public function testImage() {
    // a
    $image = $this->kt->tag('image', 'myimage.jpg');
    $tag   = kirbytext('(image: myimage.jpg)');
    
    $expected = '<img src="' . $this->url . '/myimage.jpg" />';
    $this->assertEquals($expected, $image);
    $this->assertEquals($expected, $tag);
    
    // b
    $image = $this->kt->tag('image', 'myimage.jpg', array(
      'alt'    => 'My image'
    ));
    
    $tag = kirbytext('(image: myimage.jpg alt: My image)');
    
    $expected = '<img src="' . $this->url . '/myimage.jpg" alt="My image" />';
    $this->assertEquals($expected, $image);
    $this->assertEquals($expected, $tag);
    
    // c
    $image = $this->kt->tag('image', 'myimage.jpg', array(
      'title'  => 'Super title', 
      'class'  => 'classy', 
    ));
    
    $tag = kirbytext('(image: myimage.jpg title: Super title class: classy)');
    
    $expected = '<img src="' . $this->url . '/myimage.jpg" class="classy" title="Super title" />';
    $this->assertEquals($expected, $image);
    $this->assertEquals($expected, $tag);
    
    // d
    $image = $this->kt->tag('image', 'myimage.jpg', array(
      'title'  => 'Super title', 
      'class'  => 'classy', 
      'target' => '_parent',
      'link'   => 'http://google.com'
    ));
    
    $tag = kirbytext('(image: myimage.jpg title: Super title class: classy target: _parent link: http://google.com)');
    
    $expected = '<a href="http://google.com" class="classy" title="Super title" target="_parent"><img src="' . $this->url . '/myimage.jpg" class="classy" title="Super title" /></a>';
    $this->assertEquals($expected, $image);
    $this->assertEquals($expected, $tag);
    
    // e
    $image = $this->kt->tag('image', 'myimage.jpg', array(
      'title'  => 'Super title', 
      'class'  => 'classy', 
      'target' => '_parent',
      'link'   => 'self'
    ));
    
    $tag = kirbytext('(image: myimage.jpg title: Super title class: classy target: _parent link: self)');
    
    $expected = '<a href="' . $this->url . '/myimage.jpg" class="classy" title="Super title" target="_parent"><img src="' . $this->url . '/myimage.jpg" class="classy" title="Super title" /></a>';
    $this->assertEquals($expected, $image);
    $this->assertEquals($expected, $tag);
  }
  
  public function testFile() {
    // a
    $file = $this->kt->tag('file', 'myfile.jpg');
    $tag  = kirbytext('(file: myfile.jpg)');
    
    $expected = '<a href="' . $this->url . '/myfile.jpg">myfile.jpg</a>';
    $this->assertEquals($expected, $file);
    $this->assertEquals($expected, $tag);
    
    // b
    $file = $this->kt->tag('file', 'myfile.jpg', array(
      'text'   => 'What an awesome file'
    ));
    
    $tag = kirbytext('(file: myfile.jpg text: What an awesome file)');
    
    $expected = '<a href="' . $this->url . '/myfile.jpg">What an awesome file</a>';
    $this->assertEquals($expected, $file);
    $this->assertEquals($expected, $tag);
    
    // c
    $file = $this->kt->tag('file', 'myfile.jpg', array(
      'text'   => 'What an awesome file', 
      'title'  => 'Super title', 
      'class'  => 'classy', 
      'target' => '_parent'
    ));
    
    $tag = kirbytext('(file: myfile.jpg text: What an awesome file title: Super title class: classy target: _parent)');
    
    $expected = '<a href="' . $this->url . '/myfile.jpg" class="classy" title="Super title" target="_parent">What an awesome file</a>';
    $this->assertEquals($expected, $file);
    $this->assertEquals($expected, $tag);
  }
  
  public function testTwitter() {
    // a
    $twitter = $this->kt->tag('twitter', 'bastianallgeier');
    $tag = kirbytext('(twitter: bastianallgeier)');
    
    $expected = '<a href="https://twitter.com/bastianallgeier">@bastianallgeier</a>';
    $this->assertEquals($expected, $twitter);
    $this->assertEquals($expected, $tag);
    
    // b
    $twitter = $this->kt->tag('twitter', 'bastianallgeier', array(
      'text' => 'This is my twitter account'
    ));
    
    $tag = kirbytext('(twitter: bastianallgeier text: This is my twitter account)');
    
    $expected = '<a href="https://twitter.com/bastianallgeier">This is my twitter account</a>';
    $this->assertEquals($expected, $twitter);
    $this->assertEquals($expected, $tag);
    
    // c
    $twitter = $this->kt->tag('twitter', 'bastianallgeier', array(
      'text'    => 'This is my twitter account', 
      'target'  => '_blank'
    ));
    
    $tag = kirbytext('(twitter: bastianallgeier text: This is my twitter account target: _blank)');
    
    $expected = '<a href="https://twitter.com/bastianallgeier" target="_blank">This is my twitter account</a>';
    $this->assertEquals($expected, $twitter);
    $this->assertEquals($expected, $tag);
    
    // d
    $twitter = $this->kt->tag('twitter', 'bastianallgeier', array(
      'text'    => 'This is my twitter account', 
      'target'  => '_blank',
      'rel'     => 'Twitter'
    ));
    
    $tag = kirbytext('(twitter: bastianallgeier text: This is my twitter account target: _blank rel: Twitter)');
    
    $expected = '<a href="https://twitter.com/bastianallgeier" rel="Twitter" target="_blank">This is my twitter account</a>';
    $this->assertEquals($expected, $twitter);
    $this->assertEquals($expected, $tag);
    
    // e
    $twitter = $this->kt->tag('twitter', 'bastianallgeier', array(
      'text'    => 'This is my twitter account', 
      'target'  => '_blank',
      'rel'     => 'Twitter', 
      'class'   => 'twitter',
    ));
    
    $tag = kirbytext('(twitter: bastianallgeier text: This is my twitter account target: _blank rel: Twitter class: twitter)');
    
    $expected = '<a href="https://twitter.com/bastianallgeier" class="twitter" rel="Twitter" target="_blank">This is my twitter account</a>';
    $this->assertEquals($expected, $twitter);
    $this->assertEquals($expected, $tag);
  }
  
  public function testYoutube() {
    $youtube = $this->kt->tag('youtube', 'http://www.youtube.com/watch?feature=player_embedded&v=_9tHtxOCvy4');
    $tag = kirbytext('(youtube: http://www.youtube.com/watch?feature=player_embedded&v=_9tHtxOCvy4)');
    
    $expected = '<iframe src="http://www.youtube.com/embed/_9tHtxOCvy4" frameborder="0" webkitAllowFullScreen="true" mozAllowFullScreen="true" allowFullScreen="true" width="480" height="358"></iframe>';
    $this->assertEquals($expected, $youtube);
    $this->assertEquals($expected, $tag);
  }
  
  public function testVimeo() {
    $vimeo = $this->kt->tag('vimeo', 'http://vimeo.com/52345557');
    $tag = kirbytext('(vimeo: http://vimeo.com/52345557)');
    
    $expected = '<iframe src="http://player.vimeo.com/video/52345557" frameborder="0" webkitAllowFullScreen="true" mozAllowFullScreen="true" allowFullScreen="true" width="480" height="358"></iframe>';
    $this->assertEquals($expected, $vimeo);
    $this->assertEquals($expected, $tag);
  }
  
  public function testGist() {
    $gist = $this->kt->tag('gist', 'https://gist.github.com/2924148');
    $tag = kirbytext('(gist: https://gist.github.com/2924148)');
    
    $expected = '<script src="https://gist.github.com/2924148.js"></script>';
    $this->assertEquals($expected, $gist);
    $this->assertEquals($expected, $tag);
    
    // switch markdown parsing back on
    c::set('markdown', true);
  }
}