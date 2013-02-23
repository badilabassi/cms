<?php

require_once('lib/testing_bootstrap.php');

class URITest extends PHPUnit_Framework_TestCase {
  public function __construct() {
    $this->subfolder = 'mysubfolder';
    $this->url = 'http://superurl.com/mysubfolder/fantastic/path';
    $this->uri = new KirbyURI($this->url, array(
      'subfolder' => $this->subfolder
    ));
  }
  
  public function testMethods() {
    $this->assertInstanceOf('KirbyUriParams', $this->uri->params());
    $this->assertInstanceOf('KirbyUriQuery', $this->uri->query());
    $this->assertInstanceOf('KirbyUriPath', $this->uri->path());
    
    $this->assertEquals($this->url, $this->uri->url());
    $this->assertEquals($this->subfolder, $this->uri->subfolder());
    $this->assertEquals('fantastic/path', (string)$this->uri);
    
    $this->assertEquals('http', $this->uri->scheme());
    $this->assertEquals('superurl.com', $this->uri->host());
    $this->assertEquals($this->url, $this->uri->original());
    $this->assertEquals('http://superurl.com/mysubfolder', $this->uri->baseurl());
    $this->assertEquals('fantastic/path', (string)$this->uri->path());
    $this->assertEquals('fantastic/path', $this->uri->toString());
    $this->assertEquals($this->uri->url(), $this->uri->toURL());
    $this->assertEquals('b34fa55528b70bd1dcca6f687a40602c', $this->uri->toHash());
    
    // full fledged url
    
    $url = 'http://getkirby.com/test/url/with/a/long/path/file.php/param1:test1/param2:test2?var1=test1&var2=test2';
    
    $this->uri->set($url);
    $this->assertEquals($this->uri->original(), $url);
    
    // check for a php extension
    $this->assertEquals('php', $this->uri->extension()); // ???

    // strip the path
    $this->uri->stripPath();
    $this->assertEquals(null, $this->uri->path(1));
    
    // replace parameter
    $this->uri->replaceParam('param2', 'new-param-value');
    $this->assertEquals('new-param-value', $this->uri->param('param2'));
    
    // remove a parameter
    $this->uri->removeParam('param2');
    $this->assertEquals(null, $this->uri->param('param2'));
    
    // strip params
    $this->uri->stripParams();
    $this->assertEquals(null, $this->uri->param('param1'));
    
    // add a new param
    $this->uri->param()->set('param1', 'added');
    $this->assertEquals('added', $this->uri->param('param1'));
    
    // replace a query key
    $this->uri->replaceQueryKey('var2', 'new-query-value');
    $this->assertEquals('new-query-value', $this->uri->query('var2'));
    
    // remove a query key
    $this->uri->removeQueryKey('var2');
    $this->assertEquals(null, $this->uri->query('var2'));
    
    // strip query keys
    $this->uri->stripQuery();
    $this->assertEquals(null, $this->uri->query('var1'));
    
    // add a new query key
    $this->uri->query()->set('var1', 'added');
    $this->assertEquals('added', $this->uri->query('var1'));
  }
}