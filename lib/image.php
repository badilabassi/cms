<?php 

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

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

  // cache for the attached thumb – if available
  protected $thumb = null;

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
   * Returns the attached thumb file object
   * 
   * @return object KirbyImage
   */
  public function thumb() {
    if(!is_null($this->thumb)) return $this->thumb;
    return $this->thumb = $this->parent()->thumbs()->find($this->name() . '.thumb.' . $this->extension());
  }

  /**
   * Checks if the current file has a thumb version
   * 
   * @return boolean
   */
  public function hasThumb() {
    return ($this->thumb()) ? true : false;
  }

}