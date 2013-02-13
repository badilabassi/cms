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
 
  // the parent KirbyPage object from the KirbyVariable (if available)
  protected $page = null;

  // related files
  protected $files = null;
  
  // the text object or string
  protected $text = null;
  
  // is markdown enabled?
  protected $mdown = true;
  
  // is smartypants enabled?
  protected $smartypants = true;
  
  // list of parsable tags
  protected $tags = array(
    'gist', 
    'twitter', 
    'date', 
    'image', 
    'file', 
    'link', 
    'email', 
    'youtube', 
    'vimeo', 
    'snippet'
  );
  
  // list of parsable attributes
  protected $attr = array(
    'text', 
    'file', 
    'width', 
    'height', 
    'link', 
    'popup', 
    'class', 
    'title', 
    'alt', 
    'rel', 
    'lang', 
    'target'
  );

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
  public function __construct($text = false, $mdown = true, $smartypants = true) {
      
    $this->text        = $text;  
    $this->mdown       = $mdown;
    $this->smartypants = $smartypants;
          
    // pass the parent page if available
    if(is_a($this->text, 'KirbyVariable')) $this->page = $this->text->page();

  }

  /**
   * Returns the applicable classname
   * If found it will return KirbytextExtended to prefer over the default class
   * 
   * @return string
   */
  static public function classname() {
    return class_exists('KirbytextExtended') ? 'KirbytextExtended' : 'Kirbytext';
  }

  /**
   * Returns a Kirbytext instance
   * 
   * @param mixed $text KirbyVariable object or string
   * @param boolean $mdown true: markdown is enabled, false: markdown is disabled
   * @param boolean $smartypants true: smartypants is enabled, false: smartypants is disabled
   */
  static public function instance($text = false, $mdown = true, $smartypants = true) {
    $classname = self::classname();            
    return new $classname($text, $mdown, $smartypants);    
  }
  
  /**
   * Parses and returns the text
   * This method calls parse() and code() 
   * to resolve included tags. 
   * 
   * @return string
   */
  public function get() {

    $text = preg_replace_callback('!(?=[^\]])\((' . implode('|', $this->tags) . '):(.*?)\)!i', array($this, 'parse'), (string)$this->text);
    $text = preg_replace_callback('!```(.*?)```!is', array($this, 'code'), $text);
    
    $text = ($this->mdown) ? markdown($text) : $text;
    $text = ($this->smartypants) ? smartypants($text) : $text;
    
    return $text;
    
  }

  /**
   * Used in $this->get() to parse
   * all available Kirby tags. See the list of allowed tags.
   * 
   * For each detected tag this method will look for a
   * matching method to render each tag. 
   * 
   * @param array $args A list of arguments detected by the regular expression 
   * @return string parsed text
   */
  protected function parse($args) {

    $method = strtolower(@$args[1]);
    $string = @$args[0];    
    
    if(empty($string)) return false;
    if(!method_exists($this, $method)) return $string;
    
    $replace = array('(', ')');            
    $string  = str_replace($replace, '', $string);
    $attr    = array_merge($this->tags, $this->attr);
    $search  = preg_split('!(' . implode('|', $attr) . '):!i', $string, false, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);
    $result  = array();
    $num     = 0;
    
    foreach($search AS $key) {
    
      if(!isset($search[$num+1])) break;
      
      $key   = trim($search[$num]);
      $value = trim($search[$num+1]);

      $result[ $key ] = $value;
      $num = $num+2;

    }

    return $this->$method($result);
        
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

  /**
   * Internal smart resolving for given urls. 
   * This method tries to find matching pages or files
   * for given urls.
   * 
   * @param string $url the orginal url coming from a Kirby tag
   * @param string $lang 2-char language code for multi-lang sites
   * @param boolean $metadata if set to true, this will return an array with additional meta data (the related page or file) found for the url. 
   * @return mixed
   */
  protected function url($url, $lang = false, $metadata = false) {

    $file = false;
    
    if(preg_match('!(http|https)\:\/\/!i', $url)) {
      return (!$metadata) ? $url : array(
        'url'  => $url, 
        'file' => $file
      );
    }
            
    if($files = $this->relatedFiles()) {
      $file = $files->find($url);
      $url  = ($file) ? $file->url() : url($url, $lang);
    }
            
    return (!$metadata) ? $url : array(
      'url'  => $url,
      'file' => $file
    );

  }

  /**
   * Returns the current related page object if available
   * 
   * @return object KirbyPage
   */ 
  protected function relatedPage() {
    return ($this->page) ? $this->page : site()->activePage();
  }

  /**
   * Returns related files for the related page
   * 
   * @return object KirbyFiles
   */ 
  protected function relatedFiles() {
    $object = $this->relatedPage();
    return ($object) ? $object->files() : null;
  }

  /**
   * Renders a link tag 
   * ie. (link: my/link text: my text)
   * 
   * @param array $params a list of arguments passed by the $this->parse() method
   * @return string
   */
  public function link($params) {

    $url = @$params['link'];

    // sanitize the url
    if(empty($url)) $url = '/';
        
    return Html::a($this->url($url, self::lang($params)), html(@$params['text']), array(
      'rel'    => @$params['rel'], 
      'class'  => @$params['class'], 
      'title'  => html(@$params['title']),
      'target' => self::target($params),
    )); 

  }

  /**
   * Renders an image tag 
   * ie. (image: myimage.jpg)
   * 
   * @param array $params a list of arguments passed by the $this->parse() method
   * @return string
   */
  public function image($params) {
        
    $url   = @$params['image'];
    $alt   = @$params['alt'];
    $title = @$params['title'];

    // alt is just an alternative for text
    if(!empty($params['text'])) $alt = $params['text'];
    
    // get metadata (url + file) for the image url
    $imageMeta = $this->url($url, $lang = false, $metadata = true);

    // try to get the title from the image object and use it as alt text
    if($imageMeta['file']) {
      
      if(empty($alt) && $imageMeta['file']->alt() != '') {
        $alt = $imageMeta['file']->alt();
      }

      if(empty($title) && $imageMeta['file']->title() != '') {
        $title = $imageMeta['file']->title();
      }

      // last resort for no alt text
      if(empty($alt)) $alt = $title;

    }
            
    $image = Html::img($imageMeta['url'], array(
      'width'  => @$params['width'], 
      'height' => @$params['height'], 
      'class'  => @$params['class'], 
      'title'  => html($title), 
      'alt'    => html($alt)
    ));

    if(empty($params['link'])) return $image;

    // build the href for the link
    $href = ($params['link'] == 'self') ? $url : $params['link'];
    
    return Html::a($this->url($href), $image, array(
      'rel'    => @$params['rel'], 
      'class'  => @$params['class'], 
      'title'  => html(@$params['title']), 
      'target' => self::target($params)
    ));
          
  }

  /**
   * Renders an file tag 
   * ie. (file: myfile.pdf)
   * 
   * @param array $params a list of arguments passed by the $this->parse() method
   * @return string
   */
  public function file($params) {

    $url  = @$params['file'];
    $text = @$params['text'];

    // use filename if the text is empty and make sure to 
    // ignore markdown italic underscores in filenames
    if(empty($text)) $text = str_replace('_', '\_', f::filename($url)); 

    return Html::a($this->url($url), html($text), array(
      'class'  => @$params['class'], 
      'title'  => html(@$params['title']),
      'target' => self::target($params),
    ));

  }
  
  /**
   * Renders a date tag 
   * ie. (date: year)
   * 
   * @param array $params a list of arguments passed by the $this->parse() method
   * @return string
   */
  static public function date($params) {
    $format = @$params['date'];
    return (str::lower($format) == 'year') ? date('Y') : date($format);
  }

  /**
   * Renders an email tag 
   * ie. (email: bastian@getkirby.com)
   * 
   * @param array $params a list of arguments passed by the $this->parse() method
   * @return string
   */
  static public function email($params) {
        
    return Html::email(@$params['email'], html(@$params['text']), array(
      'class' => @$params['class'],
      'title' => @$params['title']      
    ));

  }

  /**
   * Renders a twitter tag 
   * ie. (twitter: getkirby)
   * 
   * @param array $params a list of arguments passed by the $this->parse() method
   * @return string
   */
  static public function twitter($params) {
    
    // get the username
    $username = @$params['twitter'];
    
    // sanitize the username
    $username = str_replace('@', '', $username);
    
    // build the profile url
    $url = 'http://twitter.com/' . $username;

    // sanitize the link text
    $text = (empty($params['text'])) ? '@' . $username : $params['text'];

    // build the final link
    return Html::a($url, $text, array(
      'class'  => @$params['class'], 
      'title'  => @$params['title'],
      'rel'    => @$params['rel'], 
      'target' => self::target($params),
    ));

  }

  /**
   * Renders a youtube tag 
   * ie. (youtube: http://www.youtube.com/watch?v=_9tHtxOCvy4)
   * 
   * @param array $params a list of arguments passed by the $this->parse() method
   * @return string
   */
  static public function youtube($params) {

    return Embed::youtube(@$params['youtube'], array(
      'width'  => a::get($params, 'width',  c::get('kirbytext.video.width')), 
      'height' => a::get($params, 'height', c::get('kirbytext.video.height')), 
      'class'  => @$params['class']
    ));
  
  }

  /**
   * Renders a vimeo tag 
   * ie. (vimeo: http://vimeo.com/52345557)
   * 
   * @param array $params a list of arguments passed by the $this->parse() method
   * @return string
   */
  static public function vimeo($params) {

    return Embed::vimeo(@$params['vimeo'], array(
      'width'  => a::get($params, 'width',  c::get('kirbytext.video.width')), 
      'height' => a::get($params, 'height', c::get('kirbytext.video.height')), 
      'class'  => @$params['class']
    ));
          
  }

  /**
   * Renders a gist tag 
   * ie. (gist: https://gist.github.com/2924148)
   * 
   * @param array $params a list of arguments passed by the $this->parse() method
   * @return string
   */
  static public function gist($params) {
    return Embed::gist(@$params['gist'], @$params['file']);
  }

  /**
   * Embeds a flash file
   * 
   * @param string $url the url for the fla or swf file
   * @param int $width
   * @param int $height 
   * @return string
   */
  static public function flash($url, $width, $height) {

    if(!$width)  $width  = c::get('kirbytext.video.width');
    if(!$height) $height = c::get('kirbytext.video.height');

    return '<div class="video">' . Embed::flash($url, $width, $height) . '</div>';

  }

  /**
   * Returns the value for the target attribute
   * if the params contain either a popup field or a target field
   * This is used internally in methods like link(), image(), etc. 
   *
   * @param array $params
   * @return string 
   */
  static protected function target($params) {
    if(empty($params['popup']) && empty($params['target'])) return false;
    return (empty($params['popup'])) ? $params['target'] : '_blank';
  }

  /**
   * Returns the currently applicable language
   * This is used internally in methods like link()
   *
   * @param array $params
   * @return string The language code if available
   */
  static protected function lang($params) {
    // language attribute is only allowed when lang support is activated
    return (!empty($params['lang']) && c::get('lang.support')) ? $params['lang'] : false;
  }

  /**
   * Adds more parsable tags to $this->tags
   *
   * @param list A list of tags
   */
  public function addTags() {
    $args = func_get_args();
    $this->tags = array_merge($this->tags, $args);
  }

  /**
   * Adds more parsable attributes to $this->attr
   *
   * @param list A list of attributes
   */
  public function addAttributes($attr) {
    $args = func_get_args();
    $this->attr = array_merge($this->attr, $args);      
  }
  
}

