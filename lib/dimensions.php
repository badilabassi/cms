<?php 

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Dimensions
 * 
 * The dimension object is used to provide additional
 * methods for KirbyImage objects and possibly other 
 * objects with width and height to recalculate the size, 
 * get the ratio or just the width and height. 
 * 
 * @package Kirby CMS
 */
class KirbyDimensions {

  // the width of the parent object
  protected $width  = 0;

  // the height of the parent object
  protected $height = 0;

  /**
   * Constructor
   *
   * @param int $width
   * @param int $height
   */
  public function __construct($width, $height) {
    $this->width  = $width;
    $this->height = $height;
  }

  /**
   * Returns the witdh of the parent object
   * 
   * @return int
   */
  public function width() {
    return $this->width;
  }

  /**
   * Returns the height of the parent object
   * 
   * @return int
   */
  public function height() {
    return $this->height;
  }

  /**
   * Calculates and returns the ratio of the parent object
   * 
   * @return float
   */
  public function ratio() {
    return size::ratio($this->width(), $this->height());    
  }

  /**
   * Recalculates the width and height of the parent 
   * object to fit into the given box. 
   * 
   * @param int $box the max width and/or height
   * @param boolean $force If true, the parent object will be upscaled to fit the box if smaller
   * @return object returns this object with recalculated dimensions
   */
  public function fit($box, $force=false) {
    $size = size::fit($this->width(), $this->height(), $box, $force);    
    $this->width  = $size['width'];
    $this->height = $size['height'];
    return $this;
  }

  /**
   * Recalculates the width and height of the parent 
   * object to fit the given width
   * 
   * @param int $width the max width
   * @param boolean $force If true, the parent object will be upscaled to fit the width if smaller
   * @return object returns this object with recalculated dimensions
   */
  public function fitWidth($width, $force=false) {
    $size = size::fit_width($this->width(), $this->height(), $width, $force);    
    $this->width  = $size['width'];
    $this->height = $size['height'];
    return $this;      
  }

  /**
   * Recalculates the width and height of the parent 
   * object to fit the given height
   * 
   * @param int $height the max height
   * @param boolean $force If true, the parent object will be upscaled to fit the height if smaller
   * @return object returns this object with recalculated dimensions
   */
  public function fitHeight($height, $force=false) {
    $size = size::fit_height($this->width(), $this->height(), $height, $force);    
    $this->width  = $size['width'];
    $this->height = $size['height'];
    return $this;      
  }

  /**
   * Echos the dimensions as width x height
   * 
   * @return string
   */
  public function __toString() {
    return $this->width . ' x ' . $this->height;
  }

}
