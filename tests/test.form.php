<?php

require_once('bootstrap.php');
require_once(c::get('root.lib') . DS . 'html' . DS . 'form.php');

class TestOfForm extends UnitTestCase {

  function testForm() {

    $expected = '<form action="action.php" method="post" enctype="multipart/form-data">';
    $this->assertTrue(form::start('action.php', 'post', true) == $expected);

    $expected = '<input type="hidden" name="somevar" value="somevalue" />';
    $this->assertTrue(form::input('hidden', 'somevar', 'somevalue') == $expected);

    $expected = '<input type="text" name="somevar" value="somevalue" />';
    $this->assertTrue(form::text('somevar', 'somevalue') == $expected);

    $expected = '<label>my label</label>';
    $this->assertTrue(form::label('my label') == $expected);

    $expected = '<label for="test">my label</label>';
    $this->assertTrue(form::label('my label', 'test') == $expected);

    $expected = '<textarea name="somevar">somevalue</textarea>';
    $this->assertTrue(form::textarea('somevar', 'somevalue') == $expected);

    $expected = '<option value="mykey">myvalue</option>';
    $this->assertTrue(form::option('mykey', 'myvalue') == $expected);
  
    $expected = '<option value="mykey" selected="selected">myvalue</option>';
    $this->assertTrue(form::option('mykey', 'myvalue', true) == $expected);

    $expected = '<input type="radio" name="somevar" value="somevalue" />';
    $this->assertTrue(form::radio('somevar', 'somevalue') == $expected);

    $expected = '<input type="radio" name="somevar" value="somevalue" checked="checked" />';
    $this->assertTrue(form::radio('somevar', 'somevalue', true) == $expected);

    $expected = '<input type="checkbox" name="somevar" />';
    $this->assertTrue(form::checkbox('somevar') == $expected);

    $expected = '<input type="checkbox" name="somevar" checked="checked" />';
    $this->assertTrue(form::checkbox('somevar', true) == $expected);

    $expected = '<input type="file" name="myfile" />';
    $this->assertTrue(form::file('myfile') == $expected);

    $expected = '<select name="somevar">
<option value="value1" selected="selected">Value 1</option>
<option value="value2">Value 2</option>
</select>';
    
    $this->assertTrue(form::select('somevar', array(
      'value1' => 'Value 1',
      'value2' => 'Value 2'
    ), 'value1') == $expected);

    $expected = '</form>';
    $this->assertTrue(form::end() == $expected);

  }

}