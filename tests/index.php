<?php

require_once('bootstrap.php');

class AllTests extends TestSuite {

  function AllTests() {

    site(array(
      'url'          => 'http://superurl.com', 
      'subfolder'    => '',
      'root.content' => TEST_CONTENT, 
      'root.site'    => TEST_CONTENT
    ));

    $this->TestSuite('All tests');
    $files = dir::read(__DIR__);

    foreach($files as $f) {
      if(preg_match('/test\.(.*)?\.php/', $f)) {
        $this->addFile($f);      
      }
    }
  }
}

?>