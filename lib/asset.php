<?php 

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Asset
 * 
 * @package Kirby CMS
 */
class KirbyAsset {

  // the full public url to the file
  protected $url = null;

  // the full root/path of the file
  protected $root = null;

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

  // unix timestamp of the last modification date
  protected $modified = null;

  // the raw file size
  protected $size = null;

  // a human readable file size (i.e. 1 MB)
  protected $niceSize = null;

  // the mime type if detectable
  protected $mime = null;

  // cache for an exif data object
  protected $exif = null;

  // cache for the dimensions object
  protected $dimensions = null;

  // array with more details about the asset
  protected $details = null;

  /**
   * Constructor
   * 
   * @param string $url
   * @param string $root
   */
  public function __construct($url, $root = null) {
    $this->url  = $url;
    $this->root = realpath($root);
  }

  /**
   * Returns the full URL to the file
   * i.e. http://yourdomain.com/content/somefolder/somesubfolder/somefile.jpg
   *
   * @return string
   */
  public function url() {
    return $this->url;
  }

  /**
   * Returns the full root of the asset
   * 
   * @return string
   */
  public function root() {
    return $this->root;
  }

  /**
   * Returns a md5 hash of this file's root
   * 
   * @return string
   */
  public function hash() {
    return md5($this->root);
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
  public function type() {
        
    // get the cached type if available
    if(!is_null($this->type)) return $this->type;

    // get the matching fileinfo for the extension
    $info = a::get($this->filetypes(), $this->extension());

    // get the type matching to this extension
    if($info && isset($info['type'])) {
      return $this->type = $info['type'];      
    }

    // unkown file type
    return $this->type = 'unknown';

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
      // Fileinfo is prefered if available
      $finfo = finfo_open(FILEINFO_MIME_TYPE);
      $mime  = finfo_file($finfo, $this->root);
      finfo_close($finfo);
    } else if(function_exists('mime_content_type') && $mime = @mime_content_type($this->root) !== false) {
      // The mime type has already been set by the if statement!
    } else {
      // get the matching fileinfo for the extension
      $info = a::get($this->filetypes(), $this->extension(), array());

      // try to guess the mime type
      $mime = @$info['mime'];

    }

    // mark the mime as false if not available
    // otherwise the entire stuff above will be executed 
    // on each function call even if we already know it's not possible
    if($mime == null) $mime = false;

    return $this->mime = $mime;

  }

  /**
   * Returns the exif data object for this asset
   * 
   * @return object KirbyExif
   */
  public function exif() {
    if(!is_null($this->exif)) return $this->exif;
    return $this->exif = new KirbyExif($this);
  }

  /**
   * Inspects the asset file with additional
   * methods to retreive more info about it
   * 
   * @return array
   */
  public function details() {

    if(!is_null($this->details)) return $this->details;

    // try to get the asset dimensions with getimagesize
    // this should work for all major image types and some videos
    return $this->details = @getimagesize($this->root());

  }

  /**
   * Returns the dimensions object for this asset
   * 
   * @return object KirbyDimensions
   */
  public function dimensions() {

    if(!is_null($this->dimensions)) return $this->dimensions;

    // fetch details about the asset first
    $details = $this->details();

    // also set the mime type since this is more reliable
    $this->mime = a::get($details, 'mime');    

    // init and return the dimensions object with the detected width and height
    return $this->dimensions = new KirbyDimensions(a::get($details, 0, 0), a::get($details, 1, 0));

  }

  /**
   * Checks if the color mode RGB or CMYK
   * 
   * @return string
   */
  public function colorMode() {    
    switch(a::get($this->details(), 'channels')) {
      case 3:  return 'RGB';
      case 4:  return 'CMYK';
      default: return false;
    }
  }

  /**
   * Returns the color bit depth of images
   * 
   * @return int
   */
  public function colorDepth() {
    return a::get($this->details(), 'bits');
  }

  /**
   * Returns the width of the asset
   * 
   * @return int
   */
  public function width() {
    return $this->dimensions()->width();
  }

  /**
   * Returns the height of the asset
   * 
   * @return int
   */
  public function height() {
    return $this->dimensions()->height();
  }

  /**
   * Returns the ratio of the asset
   * 
   * @return int
   */
  public function ratio() {
    return $this->dimensions()->ratio();
  }

  /**
   * Returns basic file info by extension
   */
  protected function filetypes() {

    $fileinfo = array(

      // images
      'jpg'      => array('type' => 'image', 'mime' => 'image/jpeg'),
      'jpeg'     => array('type' => 'image', 'mime' => 'image/jpeg'),
      'gif'      => array('type' => 'image', 'mime' => 'image/gif'),
      'png'      => array('type' => 'image', 'mime' => 'image/png'),
      'svg'      => array('type' => 'image', 'mime' => 'image/svg+xml'),
      'ico'      => array('type' => 'image', 'mime' => 'image/x-icon'),
      'tif'      => array('type' => 'image', 'mime' => 'image/tiff'),
      'tiff'     => array('type' => 'image', 'mime' => 'image/tiff'),
      'bmp'      => array('type' => 'image', 'mime' => 'image/bmp'),

      // documents
      'txt'      => array('type' => 'document', 'mime' => 'text/plain'),
      'mdown'    => array('type' => 'document', 'mime' => 'text/plain'),
      'md'       => array('type' => 'document', 'mime' => 'text/plain'),
      'markdown' => array('type' => 'document', 'mime' => 'text/plain'),
      'pdf'      => array('type' => 'document', 'mime' => 'application/pdf'),
      'doc'      => array('type' => 'document', 'mime' => 'application/msword'),
      'docx'     => array('type' => 'document', 'mime' => 'application/msword'),
      'xls'      => array('type' => 'document', 'mime' => 'application/msexcel'),
      'xlsx'     => array('type' => 'document', 'mime' => 'application/msexcel'),
      'ppt'      => array('type' => 'document', 'mime' => 'application/mspowerpoint'),

      // archives
      'zip'      => array('type' => 'archive', 'mime' => 'application/zip'),
      'tar'      => array('type' => 'archive', 'mime' => 'application/x-tar'),
      'gz'       => array('type' => 'archive', 'mime' => 'application/x-gzip'),
      'gzip'     => array('type' => 'archive', 'mime' => 'application/x-gzip'),
      'tgz'      => array('type' => 'archive', 'mime' => 'application/gnutar'),

      // code
      'js'       => array('type' => 'code', 'mime' => 'application/javascript'),
      'css'      => array('type' => 'code', 'mime' => 'text/css'),
      'scss'     => array('type' => 'code', 'mime' => 'text/css'),
      'htm'      => array('type' => 'code', 'mime' => 'text/html'),
      'html'     => array('type' => 'code', 'mime' => 'text/html'),
      'php'      => array('type' => 'code', 'mime' => 'text/php'),
      'xml'      => array('type' => 'code', 'mime' => 'application/xml'),
      'json'     => array('type' => 'code', 'mime' => 'application/json'),

      // videos
      'mov'      => array('type' => 'video', 'mime' => 'video/quicktime'),
      'avi'      => array('type' => 'video', 'mime' => 'video/avi'),
      'ogg'      => array('type' => 'video', 'mime' => 'video/ogg'),
      'ogv'      => array('type' => 'video', 'mime' => 'video/ogg'),
      'webm'     => array('type' => 'video', 'mime' => 'video/webm'),
      'flv'      => array('type' => 'video', 'mime' => 'video/x-flv'),
      'swf'      => array('type' => 'video', 'mime' => 'application/x-shockwave-flash'),
      'mp4'      => array('type' => 'video', 'mime' => 'video/mp4'),
      'mv4'      => array('type' => 'video', 'mime' => 'video/mv4'),

      // audio
      'mp3'      => array('type' => 'audio', 'mime' => 'audio/mp3'),
      'wav'      => array('type' => 'audio', 'mime' => 'audio/wav'),
      'aif'      => array('type' => 'audio', 'mime' => 'audio/aiff'),
      'aiff'     => array('type' => 'audio', 'mime' => 'audio/aiff'),

    );

    return array_merge($fileinfo, c::get('fileinfo', array()));

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