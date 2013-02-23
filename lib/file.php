<?php 

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * File
 * 
 * The KirbyFile object is used for all files
 * in any subfolder of the content directory. 
 * It's the base file class, which can be converted
 * to KirbyImage or KirbyContent classes if appropriate
 * 
 * @package Kirby CMS
 */
class KirbyFile {

  // the parent KirbyFiles object
  protected $parent = null;

  // the full root/path of the file
  protected $root = null;
  
  // the full public url to the file
  protected $url = null;

  // the filename without directory including extension
  protected $filename = null;

  // the filename without extension 
  protected $name = null;

  // the parent directory
  protected $dir = null;
  
  // the name of the parent directory
  protected $dirname = null;
  
  // the file extension without dot (jpg, gif, etc)
  protected $extension = null;

  // the detected file type  
  protected $type = null;
  
  // the uri (url relative to the content directory)
  protected $uri = null;
  
  // unix timestamp of the last modification date
  protected $modified = null;
  
  // the raw file size
  protected $size = null;

  // a human readable file size (i.e. 1 MB)
  protected $niceSize = null;

  // the mime type if detectable
  protected $mime = null;

  // the next file
  protected $next = null;

  // the previous file
  protected $prev = null;

  // cache for all attached meta info objects
  protected $metas = null;

  // cache for the attached meta info object
  protected $meta = null;
  
  // cache for the default meta file 
  protected $defaultMeta = null;

  /**
   * Constructor
   * 
   * @param string $root The full root/path of the file
   * @param object $parent The parent KirbyFiles object
   */
  public function __construct($root, KirbyFiles $parent = null) {
    $this->root   = realpath($root);
    $this->parent = $parent;
  } 

  /**
   * Setter and getter for the parent KirbyFiles object
   * Pass a KirbyFiles object to use this as setter
   * Without a passed argument this will return the parent object
   * 
   * @param object $parent The parent KirbyFiles object 
   * @return object KirbyFiles
   */
  public function parent(KirbyFiles $parent = null) {
    if(!is_null($parent)) return $this->parent = $parent;
    return $this->parent;
  }

  /**
   * Returns the parent KirbyPage object
   * 
   * @return object KirbyPage
   */
  public function page() {
    return $this->parent()->page();
  }

  /**
   * Returns the full root/path of the file
   * 
   * @return string
   */
  public function root() {
    return $this->root;
  }

  /**
   * Returns the URI for the file. 
   * The URI is the URL to its location within the content folder
   * without the base url of the site. 
   * i.e. content/somefolder/somesubfolder/somefile.jpg
   * 
   * @return string
   */
  public function uri() {
    return $this->page()->diruri() . '/' . $this->filename();
  }

  /**
   * Returns the full URL to the file
   * i.e. http://yourdomain.com/content/somefolder/somesubfolder/somefile.jpg
   *
   * @return string
   */
  public function url() {
    if(!is_null($this->url)) return $this->url;
    return $this->url = site()->url() . '/' . $this->uri();
  }

  /**
   * Returns the filename of the file
   * i.e. somefile.jpg
   *
   * @return string
   */
  public function filename() {
    if(!is_null($this->filename)) return $this->filename;
    return $this->filename = basename($this->root);
  }

  /**
   * Returns the parent directory path
   *
   * @return string
   */
  public function dir() {
    if(!is_null($this->dir)) return $this->dir;
    return $this->dir = dirname($this->root());
  }

  /**
   * Returns the parent directory's name
   *
   * @return string
   */
  public function dirname() {
    if(!is_null($this->dirname)) return $this->dirname;
    return $this->dirname = basename($this->dir());
  }

  /**
   * Returns the name of the file without extension   
   *
   * @return string
   */
  public function name() {
    if(!is_null($this->name)) return $this->name;
    return $this->name = f::name($this->filename());
  }

  /**
   * Returns the extension of the file 
   * i.e. jpg
   *
   * @return string
   */
  public function extension() {
    if(!is_null($this->extension)) return $this->extension;
    return $this->extension = f::extension($this->filename());
  }

  /**
   * Returns the file type i.e. image
   * Is also being used as setter
   * 
   * Available file types by default are:
   * image, video, document, sound, content, meta, other
   * See the kirby/defaults.php for config options to 
   * refine type categorization
   *
   * @param string $type 
   * @return string
   */
  public function type($type = null) {
    
    // setter    
    if(!is_null($type)) return $this->type = $type;
    
    // get the cached type if available
    if(!is_null($this->type)) return $this->type;

    // check for content files
    if($this->extension() == c::get('content.file.extension', 'txt')) {
      return $this->type = 'content';
    }

    // get the matching fileinfo for the extension
    $info = a::get(c::get('fileinfo'), $this->extension());

    // get the type matching to this extension
    if($info && isset($info['type'])) {
      return $this->type = $info['type'];      
    }

    // unkown file type
    return $this->type = 'other';

  }

  /**
   * Returns the last modified date of this file
   * as unix timestamp
   * 
   * @return int
   */
  public function modified() {
    if(!is_null($this->modified)) return $this->modified;
    return $this->modified = @filectime($this->root);
  }

  /**
   * Returns the raw file size of this file
   * 
   * @return int
   */
  public function size() {
    if(!is_null($this->size)) return $this->size;
    return $this->size = f::size($this->root);
  }

  /**
   * Returns a human readble file size
   * i.e. 1.2 MB
   * 
   * @return string
   */
  public function niceSize() {
    if(!is_null($this->niceSize)) return $this->niceSize;
    return $this->niceSize = f::nice_size($this->size());
  }

  /**
   * Returns the mime type of this file
   * if detectable. i.e. image/jpeg
   * 
   * @return string
   */
  public function mime() {
    if(!is_null($this->mime)) return $this->mime;

    if(function_exists('finfo_file')) {
      $finfo = finfo_open(FILEINFO_MIME_TYPE);
      $mime = finfo_file($finfo, $this->root);
      finfo_close($finfo);
    } else if(function_exists('mime_content_type') && $mime = @mime_content_type($this->root) !== false) {
      // The mime type has already been set by the if statement!
    } else {
      // get the matching fileinfo for the extension
      $info = a::get(c::get('fileinfo'), $this->extension(), array());

      // try to guess the mime type
      $mime = @$info['mime'];

    }

    if($mime == null) $mime = false;

    return $this->mime = $mime;

  }

  // Traversing

  /**
   * Returns all siblings of this file 
   * in a KirbyFiles collection
   * 
   * @return object KirbyFiles   
   */
  public function siblings() {
    return $this->parent()->not($this->filename());
  }

  /**
   * Returns the previous file
   * 
   * @return object KirbyFile   
   */
  public function prev() {

    if(!is_null($this->prev)) return $this->prev;

    $index  = $this->parent()->indexOf($this);    
    $values = array_values($this->parent()->toArray());
    
    return $this->prev = a::get($values, $index-1);    

  }

  /**
   * Checks if there is a previous file
   * 
   * @return boolean
   */
  public function hasPrev() {
    return ($this->prev()) ? true : false;
  }

  /**
   * Returns the next file
   * 
   * @return object KirbyFile   
   */
  public function next() {

    if(!is_null($this->next)) return $this->next;

    $index  = $this->parent()->indexOf($this);    
    $values = array_values($this->parent()->toArray());
    
    return $this->next = a::get($values, $index+1);    
  
  }

  /**
   * Checks if there is a next file
   * 
   * @return boolean
   */
  public function hasNext() {
    return ($this->next()) ? true : false;
  }

  /**
   * Returns a md5 hash of this file's root
   * 
   * @return string
   */
  public function hash() {
    return md5($this->root);
  }

  // Meta information

  /**
   * Returns all available meta files for this file
   * 
   * @return object KirbyFiles
   */
  public function metas() {

    if(!is_null($this->metas)) return $this->metas;

    $metas = clone $this->page()->metas();
    $preg  = '!^' . preg_quote($this->filename()) . '!i';

    foreach($metas->toArray() as $key => $meta) {
      if(!preg_match($preg, $meta->name())) $metas->remove($key);
    }

    return $this->metas = $metas;

  }

  /**
   * Returns the meta info object
   * which will be used to fetch custom variables for the file
   * 
   * @return object KirbyContent
   */
  public function meta($lang = null) {

    // multi-language handling
    if(c::get('lang.support')) {

      // initiate the cache if not done yet
      if(is_null($this->meta) || !is_array($this->meta)) $this->meta = array();

      // get the current applicable language code
      $lang = (is_null($lang)) ? c::get('lang.current') : $lang;

      // in cache? 
      if(isset($this->meta[$lang])) return $this->meta[$lang];

      // find the matching content file, store and return it
      $meta = $this->metas()->filterBy('languageCode', $lang)->first();

      // fall back to the default language
      if(!$meta) $meta = $this->defaultMeta();
    
      // store and return the meta
      return $this->meta[$lang] = $meta;

    }

    // single language handling
    if(!is_null($this->meta)) return $this->meta;
    return $this->meta = $this->metas()->first();

  }

  /**
   * Checks if a meta file is availabel for this file
   * 
   * @return boolean
   */
  public function hasMeta($lang = null) {
    return ($this->meta($lang)) ? true : false;
  }

  /**
   * Returns the default meta info object 
   * for multi-language support
   * 
   * @return object KirbyContent
   */
  public function defaultMeta() {
    if(!is_null($this->defaultMeta)) return $this->defaultMeta;
    return $this->defaultMeta = $this->metas()->filterBy('languageCode', c::get('lang.default'))->first();
  }

  // magic getters

  /**
   * Enables getter function calls for custom fields
   * i.e. $file->title()
   * 
   * @param string $key this is auto-filled by PHP with the called method name
   * @return mixed
   */
  public function __call($key, $arguments = null) {    
    return ($this->meta()) ? $this->meta()->$key() : null;
  }

  /**
   * Enables pseudo attributes for custom fields
   * i.e. $file->title
   * 
   * @param string $key this is auto-filled by PHP with the called attribute name
   * @return mixed
   */
  public function __get($key) {
    return ($this->meta()) ? $this->meta()->$key() : null;
  }

  /**
   * Returns a full link to this file
   * Perfect for debugging in connection with echo
   * 
   * @return string
   */
  public function __toString() {
    return '<a href="' . $this->url() . '">' . $this->url() . '</a>';  
  }

}