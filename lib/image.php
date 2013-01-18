<?php 

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

// dependencies
require_once('file.php');
require_once('dimensions.php');

/**
 * Image
 * 
 * The KirbyImage object extends the KirbyFile
 * object and is used for all images inside content 
 * folders to provide additional image-related methods.
 * A KirbyImage object can only be constructed by 
 * converting an already existing KirbyFile object.
 * 
 * @package Kirby CMS
 */
class KirbyImage extends KirbyFile {

  // cache for a child KirbyDimensions object
  protected $dimensions = null;

  /**
   * Constructor
   * 
   * @param object $file A KirbyFile object
   */
  public function __construct(KirbyFile $file) {

    // copy basic parameters from the KirbyFile object
    $this->root      = $file->root();
    $this->parent    = $file->parent();
    $this->filename  = $file->filename();
    $this->extension = $file->extension();
    $this->type      = 'image';

  }

  /**
   * Initializes and returns a KirbyDimensions object
   * 
   * @return object KirbyDimensions
   */
  public function dimensions() {

    if(!is_null($this->dimensions)) return $this->dimensions;

    $size = @getimagesize($this->root());

    // also set the mime type since this is more reliable
    $this->mime = $size['mime'];

    return $this->dimensions = new KirbyDimensions($size[0], $size[1]);

  }

  /**
   * Returns the width of the image
   * 
   * @return int
   */
  public function width() {
    return $this->dimensions()->width();
  }

  /**
   * Returns the height of the image
   * 
   * @return int
   */
  public function height() {
    return $this->dimensions()->height();
  }

  /**
   * Returns the ratio of the image
   * 
   * @return int
   */
  public function ratio() {
    return $this->dimensions()->ratio();
  }

  /**
   * Recalculates the width and height of the
   * image to fit into the given box. 
   * 
   * @param int $box the max width and/or height
   * @param boolean $force If true, the image will be upscaled to fit the box if smaller
   * @return object returns this image with recalculated dimensions
   */
  public function fit($box, $force=false) {
    return $this->dimensions()->fit($box, $force);
  }

  /**
   * Recalculates the width and height of the
   * image to fit the given width
   * 
   * @param int $width the max width
   * @param boolean $force If true, the image will be upscaled to fit the width if smaller
   * @return object returns this image with recalculated dimensions
   */
  public function fitWidth($width, $force=false) {
    return $this->dimensions()->fitWidth($width, $force);
  }

  /**
   * Recalculates the width and height of the
   * image to fit the given height
   * 
   * @param int $height the max height
   * @param boolean $force If true, the image will be upscaled to fit the height if smaller
   * @return object returns this image with recalculated dimensions
   */
  public function fitHeight($height, $force=false) {
    return $this->dimensions()->fitHeight($height, $force);
  }

  /**
   * Returns the mime type of the image
   * This method is overwriting the original KirbyFile::mime
   * because getimagesize has more reliable mime type detection. 
   * 
   * @return string
   */
  public function mime() {
    
    if(!is_null($this->mime)) return $this->mime;
    
    // use the dimension getter to determine the mime type
    $this->dimensions();

    return $this->mime;

  }

}