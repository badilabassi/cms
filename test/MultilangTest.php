<?php

require_once('lib/bootstrap.php');

class MultilangTest extends PHPUnit_Framework_TestCase {
  public function __construct() {
    $this->url = 'http://superurl.com';
  }
  
  public function testMultilangSupport() {
    c::set('lang.support', true);
    
    $this->assertInstanceOf('Language', site()->language());
    $this->assertEquals('en', (string)site()->language());
    $this->assertEquals('en', site()->language()->code());
    $this->assertTrue(site()->language()->isDefault());
    $this->assertTrue(site()->language()->isActive());
    $this->assertEquals('English', site()->language()->name());
    $this->assertNull(site()->language()->locale());
    
    $this->assertEquals($this->url . '/en', site()->language()->url());
    $this->assertEquals($this->url . '/de', site()->language('de')->url());
    
    $this->assertEquals($this->url, site()->url());    
    $this->assertEquals($this->url . '/de', site()->url('de'));    
    $this->assertEquals($this->url . '/en', site()->url('en'));    
    
    $this->assertEquals('en', site()->languages()->findDefault()->code());
    $this->assertEquals('en', site()->languages()->findActive()->code());
    $this->assertEquals('en', site()->languages()->findPreferred()->code());

    $p = site()->pages()->find('tests/multilang');
    
    site(array(
      'url' => $this->url,
      'root.content' => TEST_CONTENT,
      'lang.support' => true,
      'lang.current' => 'de'
    ));
    
    $this->assertEquals('My german content file', (string)$p->content()->title());
    $this->assertEquals('This is a german text', (string)$p->content()->text());
    $this->assertEquals('This is a german text', (string)$p->text());
    $this->assertEquals('A field which only exists in English', (string)$p->super());
    $this->assertEquals('My english content file', (string)$p->content('en')->title());
    $this->assertEquals('This is an english text', (string)$p->content('en')->text());
    
    $this->assertEquals('en', $p->defaultContent()->languageCode());

    $this->assertEquals('mehrsprachig', $p->translatedUID());
    $this->assertEquals('tests/mehrsprachig', $p->translatedURI());
    $this->assertEquals($this->url . '/de/tests/mehrsprachig', $p->url());

    site(array(
      'lang.current' => 'en'
    ));

    $this->assertEquals('multilang', $p->translatedUID());
    $this->assertEquals('tests/multilang', $p->translatedURI());
    $this->assertEquals($this->url . '/en/tests/multilang', $p->url());
    
    $this->assertEquals('My english content file', (string)$p->content()->title());
    $this->assertEquals('This is an english text', (string)$p->content()->text());
    $this->assertEquals('This is an english text', (string)$p->text());
    $this->assertEquals('A field which only exists in English', (string)$p->super());
    $this->assertEquals('My german content file', (string)$p->content('de')->title());
    $this->assertEquals('This is a german text', (string)$p->content('de')->text());
    
    $this->assertEquals('content', $p->intendedTemplate());
    
    // multilang file meta support
    $image = $p->images()->find('image-01.jpg');
    $this->assertEquals('Title for an english image-01', $image->title());
    $this->assertEquals('Title for a german image-01', $image->meta('de')->title());
    
    site(array(
      'lang.current' => 'de'
    ));
    
    $this->assertEquals('Title for a german image-01', $image->title());
    $this->assertEquals('Title for an english image-01', $image->meta('en')->title());
    
    site(array(
      'lang.support' => false
    ));
    
  }
}