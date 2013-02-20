<?php

require_once('testing_bootstrap.php');

class CollectionTest extends PHPUnit_Framework_TestCase {
  public function __construct() {
    $this->data = array(
      'first'  => 'My first element',
      'second' => 'My second element',
      'third'  => 'My third element',
    );
    
    $this->collection = new KirbyCollection($this->data);
  }
  
  public function tearDown() {
    // reset the collection    
    $this->collection = new KirbyCollection($this->data);
  }
  
  public function testInitializeCollection() {
    $this->assertInstanceOf('KirbyCollection', $this->collection);
  }
  
  public function testGetters() {
    $this->assertEquals('My first element', $this->collection->first);
    $this->assertEquals('My second element', $this->collection->second);
    $this->assertEquals('My third element', $this->collection->third);
    
    $this->assertEquals('My first element', $this->collection->first());
    $this->assertEquals('My second element', $this->collection->second());
    $this->assertEquals('My third element', $this->collection->third());
    
    $this->assertEquals('My first element', $this->collection->get('first'));
    $this->assertEquals('My second element', $this->collection->get('second'));
    $this->assertEquals('My third element', $this->collection->get('third'));
  }
  
  public function testSetters() {
    $this->collection->fourth = 'My fourth element';
    $this->collection->fifth  = 'My fifth element';
    
    $this->assertEquals('My fourth element', $this->collection->fourth);
    $this->assertEquals('My fifth element', $this->collection->fifth);
    
    $this->assertEquals('My fourth element', $this->collection->fourth());
    $this->assertEquals('My fifth element', $this->collection->fifth());
    
    $this->assertEquals('My fourth element', $this->collection->get('fourth'));
    $this->assertEquals('My fifth element', $this->collection->get('fifth'));
  }
  
  public function testMethods() {
    $this->assertEquals($this->data, $this->collection->toArray());
    
    $this->assertEquals('My first element', $this->collection->first());
    $this->assertEquals('My third element', $this->collection->last());
    $this->assertEquals(3, $this->collection->count());
    $this->assertEquals('second', $this->collection->keyOf('My second element'));
    $this->assertEquals(1, $this->collection->indexOf('My second element'));
    
    // isset
    $this->assertTrue(isset($this->collection->first));
    $this->assertFalse(isset($this->collection->super));
    
    // traversing
    $this->assertEquals('My second element', $this->collection->next());
    $this->assertEquals('My third element', $this->collection->next());
    $this->assertEquals('My second element', $this->collection->prev());
    
    // nth child
    $this->assertEquals('My first element', $this->collection->nth(0));
    $this->assertEquals('My second element', $this->collection->nth(1));
    $this->assertEquals('My third element', $this->collection->nth(2));
    $this->assertFalse($this->collection->nth(3));
    
    // get all keys
    $this->assertEquals(array('first', 'second', 'third'), $this->collection->keys());
    
    // TODO: cloning methods
    
    // find elements
    $tmp = new KirbyCollection(array('first' => $this->data['first'], 'third' => $this->data['third']));
    
    $this->assertEquals($tmp, $this->collection->find('first', 'third'));
    
    $this->isUntouched();
    
    // shuffle without destroying the keys
    $this->assertInstanceOf('KirbyCollection', $this->collection->shuffle());
    
    $this->isUntouched();
    
    // filter
    $filtered = $this->collection->filter(function($element) {
      return ($element == 'My second element') ? true : false;
    });
    
    $this->assertEquals('My second element', $filtered->first());
    $this->assertEquals('My second element', $filtered->last());
    $this->assertEquals(1, $filtered->count());
    
    $this->isUntouched();
    
    // remove elements
    $this->assertEquals('My second element', $this->collection->not('first')->first());
    $this->assertEquals(1, $this->collection->not('second')->not('third')->count());
    $this->assertEquals(0, $this->collection->not('first', 'second', 'third')->count());
    
    // also check the alternative
    $this->assertEquals('My second element', $this->collection->without('first')->first());
    
    $this->isUntouched();
    
    // slice the data
    $this->assertEquals(array_slice($this->data, 1), $this->collection->slice(1)->toArray());
    $this->assertEquals(2, $this->collection->slice(1)->count());
    $this->assertEquals(array_slice($this->data, 0, 1), $this->collection->slice(0,1)->toArray());
    $this->assertEquals(1, $this->collection->slice(0,1)->count());
    
    $this->assertEquals(array_slice($this->data, 1), $this->collection->offset(1)->toArray());
    $this->assertEquals(array_slice($this->data, 0, 1), $this->collection->limit(1)->toArray());
    $this->assertEquals(array_slice($this->data, 1, 1), $this->collection->offset(1)->limit(1)->toArray());
    
    $this->isUntouched();
    
    $this->assertEquals(array_reverse($this->data, true), $this->collection->flip()->toArray());
    $this->assertEquals($this->data, $this->collection->flip()->flip()->toArray());
    
    $this->isUntouched();
    
    // unset
    unset($this->collection->first);
    
    $this->assertFalse(isset($this->collection->first));
  }
  
  private function isUntouched() {
    // the original collection must to be untouched
    $this->assertEquals($this->data, $this->collection->toArray());
  }
}