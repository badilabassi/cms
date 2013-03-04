<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

class KirbyRequestPlugin extends KirbyPlugin {

  public function onInit($arguments = array()) {
    $this->load('lib' . DS . 'request.php');
    return new KirbyRequest();
  }

}
