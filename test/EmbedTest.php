<?php

require_once('lib/testing_bootstrap.php');
require_once(TEST_KIRBY_LIB . DS . 'html' . DS . 'html.php');
require_once(TEST_KIRBY_LIB . DS . 'html' . DS . 'embed.php');
require_once(TEST_KIRBY_LIB . DS . 'html' . DS . 'form.php');

class EmbedTest extends PHPUnit_Framework_TestCase {
  public function testEmbed() {
  
$expected = '<object width="300" height="400">
<param name="movie" value="myflash.fla" />
<param name="allowScriptAccess" value="always" />
<param name="allowFullScreen" value="true" />
<embed src="myflash.fla" type="application/x-shockwave-flash" width="300" height="400" allowScriptAccess="always" allowFullScreen="true"></embed>
</object>';
    
    $this->assertEquals($expected, embed::flash('myflash.fla', 300, 400));
    
    $expected = '<iframe src="http://www.youtube.com/embed/_9tHtxOCvy4" frameborder="0" webkitAllowFullScreen="true" mozAllowFullScreen="true" allowFullScreen="true"></iframe>';
    $this->assertEquals($expected, embed::youtube('http://www.youtube.com/watch?feature=player_embedded&v=_9tHtxOCvy4'));
    
    $expected = '<iframe src="http://player.vimeo.com/video/52345557" frameborder="0" webkitAllowFullScreen="true" mozAllowFullScreen="true" allowFullScreen="true"></iframe>';
    $this->assertEquals($expected, embed::vimeo('http://vimeo.com/52345557'));
    
    $expected = '<script src="https://gist.github.com/2924148.js"></script>';
    $this->assertEquals($expected, embed::gist('https://gist.github.com/2924148'));
  }
}