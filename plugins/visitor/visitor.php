<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

class KirbyVisitorPlugin extends KirbyPlugin {

  public function onInit($arguments = array()) {

    $this->load('lib' . DS . 'visitor.php');

    return new KirbyVisitor();

  }

}
