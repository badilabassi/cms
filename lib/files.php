<?php 

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

// dependencies
require_once('collection.php');
require_once('file.php');
require_once('image.php');

/**
 * Files
 * 
 * This is the main collection object 
 * for a set of KirbyFile objects
 * 
 * @package Kirby CMS
 */
class KirbyFiles extends KirbyCollection {
  
  // the parent page object
  protected $page = null;

  // a cache of all images in this collection
  protected $images = null;

  // a cache of all documents in this collection
  protected $documents = null;

  // a cache of all videos in this collection
  protected $videos = null;

  // a cache of all audio files in this collection
  protected $audio = null;

  // a cache of all code files in this collection
  protected $code = null;

  // a cache of all unknown files in this collection
  protected $unknown = null;

  // a cache of all thumb images in this collection
  protected $thumbs = null;

  // a cache of all meta files in this collection
  protected $metas = null;

  // a cache of all content files in this collection
  protected $contents = null;

  /** 
   * Constructor
   * 
   * @param object $page The parent KirbyPage object
   */
  public function __construct($input) {
    
    if(is_a($input, 'KirbyPage')) {

      // attach the parent page
      $this->page = $input;

      foreach($input->dir()->files() as $key => $value) {

        // create a new file object
        $file = new KirbyFile($value, $this);
              
        // check for a specific file class
        $class = 'Kirby' . $file->type();

        if(class_exists($class)) $file = new $class($file);
      
        // add the file to the collection      
        $this->set($file->filename(), $file);

      }

    } else if(is_array($input)) {

      foreach($input as $file) {  
        
        if(!is_a($file, 'KirbyFile')) raise('All files in a set of KirbyFiles have to be KirbyFile objects');
        
        // add the page to the collection
        $this->_['_' . $file->filename()] = $file;
      
      }

    } else {
      raise('KirbyFiles must be constructed with a KirbyPage object or an array of KirbyFiles');
    }

    $contentFileExtension = c::get('content.file.extension', 'txt');

    // detect all meta and thumb files
    foreach($this->_ as $key => $file) {

      if($file->type() == 'content') {

        // try to find a matching file
        $result = $this->get($file->name());

        // convert the content file to a meta file
        if($result) $file->type('meta');

      } else if($file->type() == 'image' && f::extension($file->name()) == 'thumb') {
        
        $name   = f::name($file->name());
        $result = $this->get($name . '.' . $file->extension());

        if($result) $file->type('thumb');

      }

    }

  }

  /** 
   * Returns the parent page object
   * 
   * @return object KirbyPage
   */
  public function page() {
    return $this->page;
  }

  /** 
   * Returns a filtered version of this collection
   * which contains images (KirbyImage objects) only
   * 
   * @return object A new KirbyFiles collection
   */
  public function images() {
    if(!is_null($this->images)) return $this->images;
    return $this->filterBy('type', 'image');
  }

  /** 
   * Checks if this collection contains image files
   * 
   * @return boolean
   */
  public function hasImages() {
    return ($this->images()->count() > 0) ? true : false;
  }

  /** 
   * Returns a filtered version of this collection
   * which contains videos only
   * 
   * @return object A new KirbyFiles collection
   */
  public function videos() {
    if(!is_null($this->videos)) return $this->videos;
    return $this->filterBy('type', 'video');
  }

  /** 
   * Checks if this collection contains video files
   * 
   * @return boolean
   */
  public function hasVideos() {
    return ($this->videos()->count() > 0) ? true : false;
  }

  /** 
   * Returns a filtered version of this collection
   * which contains documents only
   * 
   * @return object A new KirbyFiles collection
   */
  public function documents() {
    if(!is_null($this->documents)) return $this->documents;
    return $this->filterBy('type', 'document');
  }

  /** 
   * Checks if this collection contains document files
   * 
   * @return boolean
   */
  public function hasDocuments() {
    return ($this->documents()->count() > 0) ? true : false;
  }

  /** 
   * Returns a filtered version of this collection
   * which contains audio files only
   * 
   * @return object A new KirbyFiles collection
   */
  public function audio() {
    if(!is_null($this->audio)) return $this->audio;
    return $this->filterBy('type', 'audio');
  }

  /**
   * Alternative for $this->audio()
   * 
   * @see self::audio()
   */
  public function sounds() {
    return $this->audio();
  }

  /** 
   * Checks if this collection contains audio files
   * 
   * @return boolean
   */
  public function hasAudio() {
    return ($this->audio()->count() > 0) ? true : false;
  }

  /**
   * Alternative for $this->hasAudio()
   * 
   * @see self::hasAudio()
   */
  public function hasSounds() {
    return $this->hasAudio();
  }

  /** 
   * Returns a filtered version of this collection
   * which contains unknown file types only
   * 
   * @return object A new KirbyFiles collection
   */
  public function unknown() {
    if(!is_null($this->unknown)) return $this->unknown;
    return $this->filterBy('type', 'unknown');
  }

  /**
   * Alternative for $this->unkown()
   * 
   * @see self::unknown()
   */
  public function others() {
    return $this->unkown();
  }

  /** 
   * Checks if this collection contains unknown files
   * 
   * @return boolean
   */
  public function hasUnknown() {
    return ($this->unknown()->count() > 0) ? true : false;
  }

  /**
   * Alternative for $this->hasUnkown()
   * 
   * @see self::hasUnknown()
   */
  public function hasOthers() {
    return $this->hasUnkown();
  }

  /** 
   * Returns a filtered version of this collection
   * which contains code only
   * 
   * @return object A new KirbyFiles collection
   */
  public function code() {
    if(!is_null($this->code)) return $this->code;
    return $this->filterBy('type', 'code');
  }

  /** 
   * Checks if this collection contains code files
   * 
   * @return boolean
   */
  public function hasCode() {
    return ($this->code()->count() > 0) ? true : false;
  }

  /** 
   * Returns a filtered version of this collection
   * which contains thumb images only
   * 
   * @return object A new KirbyFiles collection
   */
  public function thumbs() {
    if(!is_null($this->thumbs)) return $this->thumbs;
    return $this->filterBy('type', 'thumb');
  }

  /** 
   * Checks if this collection contains thumb files
   * 
   * @return boolean
   */
  public function hasThumbs() {
    return ($this->thumbs()->count() > 0) ? true : false;
  }

  /** 
   * Returns a filtered version of this collection
   * which contains meta files only
   * 
   * @return object A new KirbyFiles collection
   */
  public function metas() {
    if(!is_null($this->metas)) return $this->metas;
    return $this->filterBy('type', 'meta');
  }

  /** 
   * Checks if this collection contains meta files
   * 
   * @return boolean
   */
  public function hasMetas() {
    return ($this->metas()->count() > 0) ? true : false;
  }

  /** 
   * Returns a filtered version of this collection
   * which contains content files only
   * 
   * @return object A new KirbyFiles collection
   */
  public function contents() {
    if(!is_null($this->contents)) return $this->contents;    
    return $this->filterBy('type', 'content');
  }

  /** 
   * Checks if this collection contains content files
   * 
   * @return boolean
   */
  public function hasContents() {
    return ($this->contents()->count() > 0) ? true : false;
  }

  /**
   * Finds a single file or a set of multiple files by filename
   * If you pass one argument a single file is searched. 
   * If you pass multiple filenames as individual arguments, a set of files is returned
   * 
   * @param list Either a single filename or a multiple filenames as list of arguments
   * @return mixed KirbyFile, KirbyFiles or null
   */
  public function find() {
    
    $args = func_get_args();
    
    // find multiple files
    if(count($args) > 1) {
      $result = array();
      foreach($args as $arg) {
        $file = $this->find($arg);
        if($file) $result[$file->filename()] = $file;
      }      
      return (empty($result)) ? null : new KirbyFiles($result, $this->page());
    }    
    
    // find a single file
    $key = @$args[0];      
    if(!$key) return $this;
    return $this->get($key);
  
  }

  /**
   * Is a synonym for filterBy() but you can pass an array of values to filter by
   * 
   * @see filterBy()
   * @param string $key The field/key to search for
   * @param mixed $value Either a single value to search for or an array of values
   * @return mixed KirbyFile for a single $value or KirbyFiles for an array of values
   */
  public function findBy($key, $value) {

    if(is_array($value) && count($value) > 1) {
      $result = clone $this;
      foreach($result->_ as $index => $file) {
        if(!in_array($file->$key(), $value)) unset($result->_[$index]);
      }
      return $result;
    } 

    // convert a single argument array to a single argument
    if(is_array($value)) $value = $value[0];  
    
    return $this->filterBy($key, $value);  
  
  }

  /**
   * Find a single or multiple files by extension
   * 
   * @param list Either a single extension or a list of extension as individual arguments
   * @return mixed KirbyFile for a single extension or KirbyFiles for a list of extensions
   */
  public function findByExtension() {
    $value = func_get_args();
    return $this->findBy('extension', $value);
  }

  /**
   * Find a single or multiple files by type
   * 
   * @param list Either a single type or a list of types as individual arguments
   * @return mixed KirbyFile for a single type or KirbyFiles for a list of types
   */
  public function findByType() {
    $value = func_get_args();
    return $this->findBy('type', $value);
  }


  /**
   * Sorts all files in this collection by one of its fields
   *
   * @param string $field
   * @param string $direction
   * @param mixed $method
   * @return object KirbyFiles
   */
  public function sortBy($field, $direction='asc', $method=SORT_REGULAR) {        
    $self    = clone $this;
    $self->_ = a::sort($self->_, $field, $direction, $method);
    return $self;
  }


  /**
   * Returns a list of links for all files in this collection
   * This is perfect for debugging.
   * 
   * @return string
   */
  public function __toString() {
    $output = array();
    foreach($this->toArray() as $key => $file) {
      $output[] = $file . '<br />';          
    }    
    return implode("\n", $output);    
  }

}