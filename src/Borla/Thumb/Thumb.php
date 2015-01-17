<?php namespace Borla\Thumb;

// Use layer
use \Borla\Canvas\Layer;

/**
 * Thumbnails
 */

class Thumb extends \Borla\Canvas\Properties\Property {

  /**
   * Initialize
   */
  function __construct() {
    // Set default adapter
    $this->adapter = '\\Borla\\Canvas\\Adapters\\Image\\GD';
    // Set default output type
    $this->type = 'jpg';
    // Set output quality
    $this->quality = 1;
    // Set alignment
    $this->alignX = Layer::CENTER;
    $this->alignY = Layer::CENTER;
    // Set bg pixel
    $this->background = null;
  }

  /**
   * Destroy on destruct
   */
  function __destruct() {
    // Destroy
    $this->destroy();
  }

  /**
   * Create a thumbnail
   */
  static function create($source, $options = array()) {
    // Create thumb
    $thumb = new static();
    // Load
    $thumb->load($source);

    // If there's width
    if (isset($options['width']) && ($width = $options['width'])) {
      // Set width
      $thumb->width = $width;
    }
    // If there's height
    if (isset($options['height']) && ($height = $options['height'])) {
      // Set height
      $thumb->height = $height;
    }

    // Set width and height ratio
    $ratio = $thumb->image->width() / $thumb->image->height();

    // If there's width but no height
    if ($width !== false && $height === false) {
      // Set height
      $height = $width / $ratio;
    }
    // If there's height but no width
    if ($height !== false && $width === false) {
      // Set width
      $width = $height * $ratio;
    }

    // If there's both width and height
    if ($width && $height) {
      // Resize
      $thumb->resize($width, $height);
    }

    // Render and return
    return $thumb->render();
  }

  /**
   * Load an image
   */
  function load($source) {
    // Destroy first
    $this->destroy();
    // Create image from layer, use source
    $this->image = new \Borla\Canvas\Layers\Image($this->adapter, array($source));
    // Return
    return $this;
  }

  /**
   * Resize
   */
  function resize($width, $height) {
    // Fit
    $this->image->fit(new \Borla\Canvas\Properties\Bounds(
      new \Borla\Canvas\Properties\Point(0, 0),
      new \Borla\Canvas\Properties\Dimension($width, $height)
      // Set alignment
    ), $this->alignX, $this->alignY);
    // Return
    return $this;
  }

  /**
   * Render
   */
  function render() {
    // Create canvas from image layer
    $this->canvas = \Borla\Canvas\Canvas::createFromLayer($this->image);
    // Do more steps here
    // Flatten canvas
    $this->canvas->flatten();
    // Return
    return $this;
  }

  /**
   * Dump
   */
  function dump() {
    // Dump
    $this->canvas->dump($this->type, array(
      'quality'=> $this->quality
    ));
    // Return
    return $this;
  }

  /**
   * Destroy
   */
  function destroy() {
    // If there's an existing image
    if (isset($this->properties['image'])) {
      // Destroy it first
      $this->image->destroy();
    }
    // If there's canvas
    if (isset($this->properties['canvas'])) {
      // Destroy 
      $this->canvas->destroy();
    }
    // Return
    return $this;
  }

}