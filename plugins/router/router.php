<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

class KirbyRouterPlugin extends KirbyPlugin {

  public function onInit($arguments = array()) {

    $this->load(array(
      'lib' . DS . 'router.php',
      'lib' . DS . 'route.php'
    ));
    
    return new KirbyRouter(site());

  }

}
