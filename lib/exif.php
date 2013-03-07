<?php 

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Exif
 * 
 * Reads exif data from a given image asset
 * 
 * @package Kirby CMS
 */
class KirbyExif {

  // the parent asset object
  protected $asset = null;
  
  // the raw exif array
  protected $data = null;

  // the camera object with model and make
  protected $camera = null;
  
  // the location object
  protected $location = null;

  // the timestamp
  protected $timestamp = null;
    
  // the exposure value
  protected $exposure = null;
  
  // the aperture value 
  protected $aperture = null;
  
  // iso value
  protected $iso = null;

  // focal length
  protected $focalLength = null;

  // color or black/white
  protected $isColor = null;

  /**
   * Constructor
   * 
   * @param object $asset KirbyAsset 
   */
  public function __construct(KirbyAsset $asset) {
    $this->asset = $asset;
    $this->parse();
  }

  /**
   * Returns the raw data array from the parser
   * 
   * @return array
   */
  public function data() {
    return $this->data;
  }

  /**
   * Returns the Camera object
   *
   * @return object KirbyExifCamera
   */
  public function camera() {
    return $this->camera;
  }

  /**
   * Returns the location object
   *
   * @return object KirbyExifLocation
   */
  public function location() {
    return $this->location;
  }

  /**
   * Returns the timestamp
   *
   * @return string
   */
  public function timestamp() {
    return $this->timestamp;
  }

  /**
   * Returns the exposure
   *
   * @return string
   */
  public function exposure() {
    return $this->exposure;
  }

  /**
   * Returns the aperture
   *
   * @return string
   */
  public function aperture() {
    return $this->aperture;
  }

  /**
   * Checks if this is a color picture
   * 
   * @return boolean
   */
  public function isColor() {
    return $this->isColor;
  }

  /**
   * Checks if this is a bw picture
   * 
   * @return boolean
   */
  public function isBW() {
    return !$this->isColor;
  }

  /**
   * Returns the focal length
   * 
   * @return string
   */
  public function focalLength() {
    return $this->focalLength;
  }

  /**
   * Pareses and stores all relevant exif data
   */
  protected function parse() {
    
    // read the exif data of the asset if possible
    $this->data = @read_exif_data($this->asset->root());
    
    // stop on invalid exif data
    if(!is_array($this->data)) return false;

    // store the camera info
    $this->camera = new KirbyExifCamera($this->data);

    // store the location info
    $this->location = new KirbyExifLocation($this->data);

    // store the timestamp when the picture has been taken
    if(isset($this->data['DateTime'])) {
      $this->timestamp = strtotime($this->data['DateTime']);
    } else {
      $this->timestamp = a::get($this->data, 'FileDateTime', $this->asset->modified());
    }

    // exposure
    $this->exposure = a::get($this->data, 'ExposureTime');

    // iso 
    $this->iso = a::get($this->data, 'ISOSpeedRatings');

    // focal length
    $this->focalLength = a::get($this->data, 'FocalLengthIn35mmFilm');

    // aperture
    $this->aperture = @$this->data['COMPUTED']['ApertureFNumber'];

    // color or bw
    $this->isColor = @$this->data['COMPUTED']['IsColor'];

  }

}


/**
 * Returns the latitude and longitude values
 * for exif location data if available
 * 
 * @package Kirby CMS
 */
class KirbyExifLocation {

  // latitude
  protected $lat;

  // longitude
  protected $lng;

  /**
   * Constructor
   * 
   * @param array $exif The entire exif array
   */
  public function __construct($exif) {
  
    if(
      isset($exif['GPSLatitude']) && 
      isset($exif['GPSLatitudeRef']) && 
      isset($exif['GPSLongitude']) && 
      isset($exif['GPSLongitudeRef'])
    ) {
      $this->lat = $this->gps($exif['GPSLatitude'], $exif['GPSLatitudeRef']);    
      $this->lng = $this->gps($exif['GPSLongitude'], $exif['GPSLongitudeRef']);
    }

  }

  /**
   * Returns the latitude 
   * 
   * @return float
   */
  public function lat() {
    return $this->lat;
  }

  /**
   * Returns the longitude
   * 
   * @return float
   */
  public function lng() {
    return $this->lng;
  }

  /**
   * Converts the gps coordinates
   * 
   * @param string $coord
   * @param string $hemi
   * @return float
   */
  protected function gps($coord, $hemi) {

    $degrees = count($coord) > 0 ? $this->num($coord[0]) : 0;
    $minutes = count($coord) > 1 ? $this->num($coord[1]) : 0;
    $seconds = count($coord) > 2 ? $this->num($coord[2]) : 0;

    $flip = ($hemi == 'W' or $hemi == 'S') ? -1 : 1;

    return $flip * ($degrees + $minutes / 60 + $seconds / 3600);

  }

  /**
   * Converts coordinates to floats
   * 
   * @param string $part
   * @return float
   */
  protected function num($part) {

    $parts = explode('/', $part);

    if(count($parts) <= 0) return 0;
    if(count($parts) == 1) return $parts[0];

    return floatval($parts[0]) / floatval($parts[1]);
  
  }

  /**
   * Echos the entire location as lat, lng
   * 
   * @return string
   */
  public function __toString() {
    return trim(trim($this->lat() . ', ' . $this->lng(), ','));
  }


}

/**
 * Small class which hold info about the camera
 * 
 * @package Kirby CMS
 */
class KirbyExifCamera {

  protected $make;
  protected $model;

  /**
   * Constructor
   * 
   * @param string $make
   * @param string $model
   */
  public function __construct($exif) {
    $this->make  = @$exif['Make'];
    $this->model = @$exif['Model'];
  }

  /**
   * Returns the make of the camera
   * 
   * @return string
   */
  public function make() {
    return $this->make;
  }

  /**
   * Returns the camera model
   * 
   * @return string
   */
  public function model() {
    return $this->model;
  }

  /**
   * Returns the full make + model name
   * 
   * @return string
   */
  public function __toString() {
    return trim($this->make . ' ' . $this->model);
  }

}