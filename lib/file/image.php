<?php 

namespace Kirby\CMS\File;

use Kirby\CMS\File;

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Image
 * 
 * The Image object extends the File
 * object and is used for all images inside content 
 * folders to provide additional image-related methods.
 * A Image object can only be constructed by 
 * converting an already existing File object.
 * 
 * @package   Kirby CMS
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://getkirby.com/license
 */
class Image extends File {

  // cache for the attached thumb – if available
  protected $thumb = null;

  /**
   * Constructor
   * 
   * @param object $file A File object
   */
  public function __construct(File $file) {

    // copy basic parameters from the File object
    $this->root      = $file->root();
    $this->id        = $file->id();
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
   * @return object Image
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

  /**
   * Returns a more readable dump array for the dump() helper
   * 
   * @return array
   */
  public function __toDump() {

    return array_merge(parent::__toDump(), array(
      'thumb'  => $this->thumb() ? $this->thumb()->uri() : false,
      'width'  => $this->width(),
      'height' => $this->height(),
    ));

  }

}