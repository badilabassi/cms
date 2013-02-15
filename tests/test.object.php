<?php

require_once('bootstrap.php');
require_once(c::get('root.lib') . DS . 'object.php');

class CustomObject extends KirbyObject {
  
  function setUsername($value) {
    $this->write('username', $value . ' + custom setter');  
  }
  
  function getUsername() {
    return $this->read('username') . ' + custom getter';
  }
  
}

class TestOfObject extends UnitTestCase {

  function testInitializeObject() {
    
    $this->data = array(
      'username' => 'bastian',
      'email'    => 'bastian@getkirby.com',
      'password' => 'this is so secret', 
    );
    
    $this->object = new KirbyObject($this->data);
    
    $this->assertIsA($this->object, 'KirbyObject');
            
  }

  function testGetters() {
    
    $this->assertTrue($this->object->username == 'bastian');
    $this->assertTrue($this->object->email == 'bastian@getkirby.com');
    $this->assertTrue($this->object->password == 'this is so secret');

    $this->assertTrue($this->object->get('username') == 'bastian');
    $this->assertTrue($this->object->get('email') == 'bastian@getkirby.com');
    $this->assertTrue($this->object->get('password') == 'this is so secret');

    $this->assertTrue($this->object->username() == 'bastian');
    $this->assertTrue($this->object->email() == 'bastian@getkirby.com');
    $this->assertTrue($this->object->password() == 'this is so secret');
    
  }

  function testSetters() {
    
    $this->object->fullname = 'Bastian Allgeier';
    $this->object->twitter  = '@bastianallgeier';
    
    $this->assertTrue($this->object->fullname == 'Bastian Allgeier');
    $this->assertTrue($this->object->twitter  == '@bastianallgeier');

    $this->assertTrue($this->object->get('fullname') == 'Bastian Allgeier');
    $this->assertTrue($this->object->get('twitter')  == '@bastianallgeier');

    $this->assertTrue($this->object->fullname() == 'Bastian Allgeier');
    $this->assertTrue($this->object->twitter()  == '@bastianallgeier');

    // special setting stuff
    $this->object->{15} = 'super test';
    $this->assertTrue($this->object->{15} == 'super test');
    
    $this->object->_ = 'another super test';
    $this->assertTrue($this->object->_ == 'another super test');
        
    // set all values at once
    $this->restoreObject();
        
    // get all
    $this->assertTrue($this->object->get() == $this->data);
        
    $this->assertTrue($this->object->toArray() == $this->data);
    $this->assertTrue($this->object->toJSON() == json_encode($this->data));
    
    unset($this->object->username);
    $this->assertFalse(isset($this->object->username));
    $this->assertTrue($this->object->username == null);
    
    $this->restoreObject();

    $this->assertTrue($this->object->keys() == array('username', 'email', 'password'));
    
    // test to string
    $this->assertTrue((string)$this->object == a::show($this->data, false));
                
  }

  function testCustomObject() {
    
    $object = new CustomObject($this->data);
    
    // test custom setters and getters    
    $this->assertTrue($object->read('username') == 'bastian + custom setter');
    $this->assertTrue($object->get('username') == 'bastian + custom setter + custom getter');

    $object->username = 'peter';

    $this->assertTrue($object->read('username') == 'peter + custom setter');
    $this->assertTrue($object->get('username') == 'peter + custom setter + custom getter');
            
  }

  function restoreObject() {

    // set all values at once
    $this->object->reset($this->data);
      
  }

}