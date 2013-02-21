<?php

require_once('testing_bootstrap.php');

class CustomObject extends KirbyObject {
  function setUsername($value) {
    $this->write('username', $value . ' + custom setter');  
  }
  
  function getUsername() {
    return $this->read('username') . ' + custom getter';
  }
}

class ObjectTest extends PHPUnit_Framework_TestCase {
  public function __construct() {
    $this->data = array(
      'username' => 'bastian',
      'email'    => 'bastian@getkirby.com',
      'password' => 'this is so secret', 
    );
    
    $this->object = new KirbyObject($this->data);
  }
  
  public function testInitializeObject() {
    $this->assertInstanceOf('KirbyObject', $this->object);        
  }
  
  public function testGetters() {
    $this->assertEquals('bastian', $this->object->username);
    $this->assertEquals('bastian@getkirby.com', $this->object->email);
    $this->assertEquals('this is so secret', $this->object->password);
    
    $this->assertEquals('bastian', $this->object->get('username'));
    $this->assertEquals('bastian@getkirby.com', $this->object->get('email'));
    $this->assertEquals('this is so secret', $this->object->get('password'));
    
    $this->assertEquals('bastian', $this->object->username());
    $this->assertEquals('bastian@getkirby.com', $this->object->email());
    $this->assertEquals('this is so secret', $this->object->password());
  }
  
  public function testSetters() {
    $this->object->fullname = 'Bastian Allgeier';
    $this->object->twitter  = '@bastianallgeier';
    
    $this->assertEquals('Bastian Allgeier', $this->object->fullname);
    $this->assertEquals('@bastianallgeier', $this->object->twitter );
    
    $this->assertEquals('Bastian Allgeier', $this->object->get('fullname'));
    $this->assertEquals('@bastianallgeier', $this->object->get('twitter') );
    
    $this->assertEquals('Bastian Allgeier', $this->object->fullname());
    $this->assertEquals('@bastianallgeier', $this->object->twitter() );
    
    // special setting stuff
    $this->object->{15} = 'super test';
    $this->assertEquals('super test', $this->object->{15});
    
    $this->object->_ = 'another super test';
    $this->assertEquals('another super test', $this->object->_);
    
    // set all values at once
    $this->restoreObject();
    
    // get all
    $this->assertEquals($this->data, $this->object->get());
    
    $this->assertEquals($this->data, $this->object->toArray());
    $this->assertEquals(json_encode($this->data), $this->object->toJSON());
    
    unset($this->object->username);
    $this->assertFalse(isset($this->object->username));
    $this->assertNull($this->object->username);
    
    $this->restoreObject();
    
    $this->assertEquals(array('username', 'email', 'password'), $this->object->keys());
    
    // test to string
    $this->assertEquals(a::show($this->data, false), (string)$this->object);
  }
  
  public function testCustomObject() {
    $object = new CustomObject($this->data);
    
    // test custom setters and getters    
    $this->assertEquals('bastian + custom setter', $object->read('username'));
    $this->assertEquals('bastian + custom setter + custom getter', $object->get('username'));
    
    $object->username = 'peter';
    
    $this->assertEquals('peter + custom setter', $object->read('username'));
    $this->assertEquals('peter + custom setter + custom getter', $object->get('username'));
  }
  
  private function restoreObject() {
    // set all values at once
    $this->object->reset($this->data);
  }
}