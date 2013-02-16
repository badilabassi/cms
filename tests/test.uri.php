<?php

require_once('bootstrap.php');

class TestOfURI extends UnitTestCase {

  function testInitializeURI() {

    $this->subfolder = 'mysubfolder';
    $this->url = 'http://superurl.com/mysubfolder/fantastic/path';
    $this->uri = new KirbyURI($this->url, array(
      'subfolder' => $this->subfolder
    ));

  }

  function testMethods() {

    $this->assertIsA($this->uri->params(), 'KirbyUriParams');
    $this->assertIsA($this->uri->query(), 'KirbyUriQuery');
    $this->assertIsA($this->uri->path(), 'KirbyUriPath');

    $this->assertTrue($this->uri->url() == $this->url);
    $this->assertTrue($this->uri->subfolder() == $this->subfolder);
    $this->assertTrue((string)$this->uri == 'fantastic/path');

    $this->assertTrue($this->uri->scheme() == 'http');
    $this->assertTrue($this->uri->host() == 'superurl.com');
    $this->assertTrue($this->uri->original() == $this->url);
    $this->assertTrue($this->uri->baseurl() == 'http://superurl.com/mysubfolder');
    $this->assertTrue($this->uri->extension() == 'php'); // ???
    $this->assertTrue((string)$this->uri->path() == 'fantastic/path');
    $this->assertTrue($this->uri->toString() == 'fantastic/path');
    $this->assertTrue($this->uri->toURL() == $this->uri->url());
    $this->assertTrue($this->uri->toHash() == 'b34fa55528b70bd1dcca6f687a40602c');

    // full fledged url

    $url = 'http://getkirby.com/test/url/with/a/long/path/file.php/param1:test1/param2:test2?var1=test1&var2=test2';

    $this->uri->set($url);
    $this->assertTrue($url == $this->uri->original());

    // strip the path
    $this->uri->stripPath();
    $this->assertTrue($this->uri->path(1) == null);

    // replace parameter
    $this->uri->replaceParam('param2', 'new-param-value');
    $this->assertTrue($this->uri->param('param2') == 'new-param-value');

    // remove a parameter
    $this->uri->removeParam('param2');
    $this->assertTrue($this->uri->param('param2') == null);

    // strip params
    $this->uri->stripParams();
    $this->assertTrue($this->uri->param('param1') == null);

    // add a new param
    $this->uri->param()->set('param1', 'added');
    $this->assertTrue($this->uri->param('param1') == 'added');

    // replace a query key
    $this->uri->replaceQueryKey('var2', 'new-query-value');
    $this->assertTrue($this->uri->query('var2') == 'new-query-value');

    // remove a query key
    $this->uri->removeQueryKey('var2');
    $this->assertTrue($this->uri->query('var2') == null);

    // strip query keys
    $this->uri->stripQuery();
    $this->assertTrue($this->uri->query('var1') == null);

    // add a new query key
    $this->uri->query()->set('var1', 'added');
    $this->assertTrue($this->uri->query('var1') == 'added');

  }


}