<?php

require_once('bootstrap.php');

class TestOfCollection extends UnitTestCase {

  function testInitializeCollection() {

    $this->data = array(
      'first'  => 'My first element',
      'second' => 'My second element',
      'third'  => 'My third element',
    );

    $this->collection = new KirbyCollection($this->data);
    $this->assertIsA($this->collection, 'KirbyCollection');

  }

  function testGetters() {

    $this->assertTrue($this->collection->first == 'My first element');
    $this->assertTrue($this->collection->second == 'My second element');
    $this->assertTrue($this->collection->third == 'My third element');

    $this->assertTrue($this->collection->first() == 'My first element');
    $this->assertTrue($this->collection->second() == 'My second element');
    $this->assertTrue($this->collection->third() == 'My third element');

    $this->assertTrue($this->collection->get('first') == 'My first element');
    $this->assertTrue($this->collection->get('second') == 'My second element');
    $this->assertTrue($this->collection->get('third') == 'My third element');
  
  }

  function testSetters() {
    
    $this->collection->fourth = 'My fourth element';
    $this->collection->fifth  = 'My fifth element';
    
    $this->assertTrue($this->collection->fourth == 'My fourth element');
    $this->assertTrue($this->collection->fifth == 'My fifth element');

    $this->assertTrue($this->collection->fourth() == 'My fourth element');
    $this->assertTrue($this->collection->fifth() == 'My fifth element');

    $this->assertTrue($this->collection->get('fourth') == 'My fourth element');
    $this->assertTrue($this->collection->get('fifth') == 'My fifth element');
    
    // reset the collection    
    $this->collection = new KirbyCollection($this->data);    
        
  }
  
  function testMethods() {
    
    $this->assertTrue($this->collection->toArray() === $this->data);

    $this->assertTrue($this->collection->first() == 'My first element');
    $this->assertTrue($this->collection->last() == 'My third element');
    $this->assertTrue($this->collection->count() == 3);
    $this->assertTrue($this->collection->keyOf('My second element') == 'second');
    $this->assertTrue($this->collection->indexOf('My second element') == 1);
    
    // isset
    $this->assertTrue(isset($this->collection->first));
    $this->assertFalse(isset($this->collection->super));
    
    // traversing
    $this->assertTrue($this->collection->next() == 'My second element');
    $this->assertTrue($this->collection->next() == 'My third element');
    $this->assertTrue($this->collection->prev() == 'My second element');
    
    // nth child
    $this->assertTrue($this->collection->nth(0) == 'My first element');
    $this->assertTrue($this->collection->nth(1) == 'My second element');
    $this->assertTrue($this->collection->nth(2) == 'My third element');
    $this->assertFalse($this->collection->nth(3));

    // get all keys
    $this->assertTrue($this->collection->keys() == array('first', 'second', 'third'));    
    
    // cloning methods

    // find elements
    
    $tmp = new KirbyCollection(array('first' => $this->data['first'], 'third' => $this->data['third']));
        
    $this->assertTrue($this->collection->find('first', 'third') == $tmp);

    $this->isUntouched();    

    // shuffle without destroying the keys
    $this->assertIsA($this->collection->shuffle(), 'KirbyCollection');     

    $this->isUntouched();    
    
    // filter
    $filtered = $this->collection->filter(function($element) {
      return ($element == 'My second element') ? true : false;
    });
    
    $this->assertTrue($filtered->first() == 'My second element');
    $this->assertTrue($filtered->last() == 'My second element');
    $this->assertTrue($filtered->count() == 1);
        
    $this->isUntouched();    
    
    // remove elements
    $this->assertTrue($this->collection->not('first')->first() == 'My second element');
    $this->assertTrue($this->collection->not('second')->not('third')->count() == 1);
    $this->assertTrue($this->collection->not('first', 'second', 'third')->count() == 0);

    // also check the alternative
    $this->assertTrue($this->collection->without('first')->first() == 'My second element');
    
    $this->isUntouched();    

    // slice the data
    $this->assertTrue($this->collection->slice(1)->toArray() == array_slice($this->data, 1));
    $this->assertTrue($this->collection->slice(1)->count() == 2);
    $this->assertTrue($this->collection->slice(0,1)->toArray() == array_slice($this->data, 0, 1));
    $this->assertTrue($this->collection->slice(0,1)->count() == 1);

    $this->assertTrue($this->collection->offset(1)->toArray() == array_slice($this->data, 1));
    $this->assertTrue($this->collection->limit(1)->toArray() == array_slice($this->data, 0, 1));
    $this->assertTrue($this->collection->offset(1)->limit(1)->toArray() == array_slice($this->data, 1, 1));

    $this->isUntouched();    

    $this->assertTrue($this->collection->flip()->toArray() == array_reverse($this->data, true));
    $this->assertTrue($this->collection->flip()->flip()->toArray() == $this->data);
    
    $this->isUntouched();    

    // unset
    unset($this->collection->first);
    
    $this->assertFalse(isset($this->collection->first));
                        
  }

  function isUntouched() {
    // the original collection must to be untouched    
    $this->assertTrue($this->collection->toArray() === $this->data);
  }

}