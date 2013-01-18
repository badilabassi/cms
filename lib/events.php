<?php

class KirbyEvents {

  static $events = array();

  static public function on($event, $callback) {
    if(!isset(static::$events[$event])) static::$events[$event] = array();
    if(is_array($callback)) {
      // attach all passed events at once
      static::$events[$event] = array_merge(static::$events[$event], $callback);
    } else {
      // attach a single new event
      static::$events[$event][] = $callback;
    }
  }

  public function trigger($event) {
    if(!empty(static::$events[$event])) {
      foreach(static::$events[$event] as $callback) {
        if(is_string($callback) && method_exists($this, $callback)) {
          return $this->$callback();
        } else if(is_callable($callback)) {
          return $callback($this); 
        }
      } 
    }
  }

}