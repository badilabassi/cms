<?php

require_once('bootstrap.php');

class AllTests extends TestSuite {

  function AllTests() {
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