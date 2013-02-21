<?php

require_once('testing_bootstrap.php');
require_once(TEST_KIRBY_LIB . DS . 'html' . DS . 'form.php');

class FormTest extends PHPUnit_Framework_TestCase {
  public function testForm() {
    $expected = '<form action="action.php" method="post" enctype="multipart/form-data">';
    $this->assertEquals($expected, form::start('action.php', 'post', true));
    
    $expected = '<input type="hidden" name="somevar" value="somevalue" />';
    $this->assertEquals($expected, form::input('hidden', 'somevar', 'somevalue'));
    
    $expected = '<input type="text" name="somevar" value="somevalue" />';
    $this->assertEquals($expected, form::text('somevar', 'somevalue'));
    
    $expected = '<label>my label</label>';
    $this->assertEquals($expected, form::label('my label'));
    
    $expected = '<label for="test">my label</label>';
    $this->assertEquals($expected, form::label('my label', 'test'));
    
    $expected = '<textarea name="somevar">somevalue</textarea>';
    $this->assertEquals($expected, form::textarea('somevar', 'somevalue'));
    
    $expected = '<option value="mykey">myvalue</option>';
    $this->assertEquals($expected, form::option('mykey', 'myvalue'));
    
    $expected = '<option value="mykey" selected="selected">myvalue</option>';
    $this->assertEquals($expected, form::option('mykey', 'myvalue', true));
    
    $expected = '<input type="radio" name="somevar" value="somevalue" />';
    $this->assertEquals($expected, form::radio('somevar', 'somevalue'));
    
    $expected = '<input type="radio" name="somevar" value="somevalue" checked="checked" />';
    $this->assertEquals($expected, form::radio('somevar', 'somevalue', true));
    
    $expected = '<input type="checkbox" name="somevar" />';
    $this->assertEquals($expected, form::checkbox('somevar'));
    
    $expected = '<input type="checkbox" name="somevar" checked="checked" />';
    $this->assertEquals($expected, form::checkbox('somevar', true));
    
    $expected = '<input type="file" name="myfile" />';
    $this->assertEquals($expected, form::file('myfile'));
    
    $expected = '<select name="somevar">
<option value="value1" selected="selected">Value 1</option>
<option value="value2">Value 2</option>
</select>';
    
    $this->assertEquals($expected, form::select('somevar', array(
      'value1' => 'Value 1',
      'value2' => 'Value 2'
    ), 'value1'));

    $expected = '</form>';
    $this->assertEquals($expected, form::end());
  }
}