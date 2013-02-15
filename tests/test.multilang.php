<?php

require_once('bootstrap.php');
require_once(c::get('root.lib') . DS . 'site.php');

class TestOfMultilang extends UnitTestCase {

  function __construct() {
    $this->url = 'http://superurl.com';
  }

  function testMultilangSupport() {

    c::set('lang.support', true);

    $this->assertIsA(site()->language(), 'KirbyLanguage');
    $this->assertTrue((string)site()->language() == 'en');
    $this->assertTrue(site()->language()->code() == 'en');
    $this->assertTrue(site()->language()->isDefault());
    $this->assertTrue(site()->language()->isActive());
    $this->assertTrue(site()->language()->name() == 'English');
    $this->assertTrue(site()->language()->locale() == null);

    $this->assertTrue(site()->language()->url() == $this->url . '/en');
    $this->assertTrue(site()->language('de')->url() == $this->url . '/de');

    $this->assertTrue(site()->url() == $this->url);    
    $this->assertTrue(site()->url('de') == $this->url . '/de');    
    $this->assertTrue(site()->url('en') == $this->url . '/en');    

    $this->assertTrue(site()->languages()->findDefault()->code() == 'en');
    $this->assertTrue(site()->languages()->findActive()->code() == 'en');
    $this->assertTrue(site()->languages()->findPreferred()->code() == 'en');
 
    site(array(
      'url' => $this->url,
      'root.content' => TEST_CONTENT,
      'lang.support' => true,
    ));

    // TODO: this must be passable from site params array
    c::set('lang.current', 'de');

    $p = site()->pages()->find('tests/multilang');

    $this->assertTrue((string)$p->content()->title() == 'My german content file');
    $this->assertTrue((string)$p->content()->text() == 'This is a german text');
    $this->assertTrue((string)$p->text() == 'This is a german text');
    $this->assertTrue((string)$p->super() == 'A field which only exists in English');
    $this->assertTrue((string)$p->content('en')->title() == 'My english content file');
    $this->assertTrue((string)$p->content('en')->text() == 'This is an english text');

    c::set('lang.current', 'en');

    $this->assertTrue((string)$p->content()->title() == 'My english content file');
    $this->assertTrue((string)$p->content()->text() == 'This is an english text');
    $this->assertTrue((string)$p->text() == 'This is an english text');
    $this->assertTrue((string)$p->super() == 'A field which only exists in English');
    $this->assertTrue((string)$p->content('de')->title() == 'My german content file');
    $this->assertTrue((string)$p->content('de')->text() == 'This is a german text');

    $this->assertTrue($p->intendedTemplate() == 'content');

    c::set('lang.support', false);

  }

}