<?php 

class KirbyPlugins {

  protected $plugins = array();
  protected $packages = array();

  public function add($name, $object) {

    if(isset($this->plugins[$name])) raise('The plugin "' . $name . '" already exists. Please choose a different name');
    $this->plugins[$name] = $object;

  }

  public function has($name) {
    return isset($this->plugins[$name]);
  }

  public function __call($name, $arguments = null) {
    return a::get($this->plugins, $name);
  }

}