<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

class KirbyVisitorPlugin extends KirbyPlugin {

  public function onInit() {

    $this->load('lib' . DS . 'visitor.php');

    return new KirbyVisitor();

  }

}
