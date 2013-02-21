<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Kirbytext
 *
 * Kirbytext is Kirby's extended version of Markdown
 * It offers a lot of additional tags to simplify 
 * editing in content file. 
 * 
 * @package Kirby CMS
 */
class Kirbytext {
  
  // an array with options
  protected $options = array();

  // the parent KirbyPage object from the KirbyVariable (if available)
  protected $page = null;
  
  // the text object or string
  protected $text = null;
    
  /**
   * Constructor
   * 
   * To initialize the parser, always use the static Kirbytext::instance() method
   * This will make sure a custom KirbytextExtended class will be used if available
   * 
   * @param mixed $text KirbyVariable object or string
   * @param boolean $mdown true: markdown is enabled, false: markdown is disabled
   * @param boolean $smartypants true: smartypants is enabled, false: smartypants is disabled
   */
  public function __construct($text = false, $params = array()) {
    
    // by default markdown and smartypants are activated  
    $defaults = array(
      'markdown'    => true,
      'smartypants' => true, 
    );

    // enable legacy enabling for markdown with second param
    // enabling smartypants with the third argument is no longer possible
    if(is_bool($params)) {

      $markdown = $params;
      $params   = array(
        'markdown' => $markdown
      );

    }

    $this->text    = $text;  
    $this->options = array_merge($defaults, $params);
          
    // pass the parent page if available
    if(is_a($this->text, 'KirbyVariable')) $this->page = $this->text->page();

  }

  /**
   * Returns a Kirbytext instance
   * 
   * @param mixed $text KirbyVariable object or string
   * @param array $options array: array('markdown' => true, 'smartypants' => true, 'widont' => true)
   */
  static public function instance($text = false, $options = array()) {
    return new self($text, $options);    
  }

  /**
   * Returns the active page object
   * 
   * @return object KirbyPage
   */
  public function page() {
    return ($this->page) ? $this->page : site()->activePage();
  }
  
  /**
   * Parses and returns the text
   * This method calls parse() and code() 
   * to resolve included tags and applies markdown
   * and smartypants if activated. 
   * 
   * @return string
   */
  public function get() {

    $text = preg_replace_callback('!(?=[^\]])\([a-z0-9]+:.*?\)!i', array($this, 'parse'), (string)$this->text);
    $text = preg_replace_callback('!```(.*?)```!is', array($this, 'code'), $text);

    $text = ($this->options['markdown'])    ? markdown($text)    : $text;
    $text = ($this->options['smartypants']) ? smartypants($text) : $text;

    // unwrap single images, which are wrapped with p elements
    if(c::get('kirbytext.unwrapImages')) $text = preg_replace('!\<p>(<img.*?\/>)<\/p>!', '$1', $text);

    return $text;
    
  }

  /**
   * Used in $this->get() to parse
   * all available Kirby tags. See the list of installed tags in kirby/tags
   * 
   * For each detected tag this method will look for a
   * matching file and class to render each tag. 
   * 
   * @param array $args A list of arguments detected by the regular expression. The first value in the array is the entire tag
   * @return string parsed text
   */
  protected function parse($args) {

    $tag   = @$args[0];
    $name  = preg_replace('!\(([a-z0-9]+)\:.*$!i', '$1', $tag);
    $class = $this->tagclass($name);

    // if the class is not available, return the entire tag
    if(!$class) return $tag;

    // initialize the tag class
    $object = new $class(array(
      'name'      => $name, 
      'tag'       => $tag,
      'kirbytext' => $this
    ));

    // return the generated html
    return $object->html();
        
  }

  /**
   * Renders a tag by passing value and attributes manually
   * 
   * @param string $name The name of the tag 
   * @param string $value The main value for the tag
   * @param array $attr An optional associative array of attributes
   * @return string The generated html
   */
  public function tag($name, $value, $attr = array()) {

    // load the tag class
    $class = $this->tagclass($name);

    if(!$class) return false;

    // initialize the tag class
    $object = new $class(array(
      'name'      => $name, 
      'value'     => $value, 
      'attr'      => $attr,
      'kirbytext' => $this
    ));

    // return the generated html
    return $object->html();

  }

  /**
   * Detects and loads the matching tag class
   * 
   * @param string $name
   * @return object KirbyTag instance
   */
  public function tagclass($name) {

    $file  = ROOT_SITE_TAGS . DS . $name . '.php';
    $class = 'kirbytext' . $name . 'tag';

    if(!file_exists($file)) {
      $file  = ROOT_KIRBY_TAGS . DS . $name . '.php';
    }

    // return the entire tag if the class is not available
    if(!file_exists($file)) return false;

    // load the class file
    require_once($file);
    return $class;

  }

  /**
   * Used in $this->get() to parse
   * special, Github-style code blocks 
   * 
   * This methods looks for a external highlight function
   * and uses that to add code highlighting to the parsed 
   * block if available. 
   * 
   * @param string $code
   * @return string parsed text
   */
  protected function code($code) {
    
    $code = @$code[1];
    $lines = explode("\n", $code);
    $first = trim(array_shift($lines));
    $code  = implode("\n", $lines);
    $code  = trim($code);

    if(function_exists('highlight')) {
      $result  = '<pre class="highlight ' . $first . '">';
      $result .= '<code>';
      $result .= highlight($code, (empty($first)) ? 'php-html' : $first);
      $result .= '</code>';
      $result .= '</pre>';
    } else {
      $result  = '<pre class="' . $first . '">';
      $result .= '<code>';
      $result .= htmlspecialchars($code);
      $result .= '</code>';
      $result .= '</pre>';
    }
    
    return $result;
    
  }
  
}

