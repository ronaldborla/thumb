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
    $this->background = false;
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

    // Set properties
    $properties = array(
      'type',
      'quality',
      'alignX',
      'alignY',
      'background'
    );
    // Loop
    foreach ($properties as $property) {
      // If set
      if (isset($options[$property])) {
        // Set it
        $thumb->$property = $options[$property];
      }
    }

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
    // Resize
    $thumb->resize();

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
  function resize($width = false, $height = false) {
    // If there's no width
    if (!$width) {
      // Check if there's width
      if (isset($this->properties['width']) && $this->properties['width']) {
        // Set width
        $width = $this->width;
      }
    }
    // If there's no height
    if (!$height) {
      // Check if there's height
      if (isset($this->properties['height']) && $this->properties['height']) {
        // Set height
        $height = $this->height;
      }
    }
    // Get ratio
    $ratio = $this->image->width() / $this->image->height();
    // If there's width but no height
    if ($width && !$height) {
      // Set height
      $height = $width / $ratio;
    }
    // If there's height but no width
    if ($height && !$width) {
      // Set width
      $width = $height * $ratio;
    }

    // If there's width and height
    if ($width && $height) {
      // Fit
      $this->image->fit(new \Borla\Canvas\Properties\Bounds(
        new \Borla\Canvas\Properties\Point(0, 0),
        new \Borla\Canvas\Properties\Dimension($width, $height)
        // Set alignment
      ), $this->alignX, $this->alignY);
    }
    // Return
    return $this;
  }

  /**
   * Render
   */
  function render() {
    // Create canvas from image layer
    $this->canvas = new \Borla\Canvas\Canvas($this->adapter, array($this->image->dimension()));
    // If there's background
    if ($this->background) {
      // Get pixel
      $pixel = static::colorToPixel($this->background);
      // Set background
      $this->canvas->fill($pixel);
    }
    // Add image layer
    $this->canvas->addLayer($this->image);
    // Flatten canvas
    $this->canvas->flatten();
    // Return
    return $this;
  }

  /**
   * Dump
   */
  function dump($type = false, $quality = false) {
    // If there's no type
    if (!$type) {
      // Set type
      $type = $this->type;
    }
    // If there's no quality
    if ($quality === false) {
      // Set quality
      $quality = $this->quality;
    }
    // Dump
    $this->canvas->dump($type, array(
      'quality'=> $quality
    ));
    // Return
    return $this;
  }

  /**
   * Save
   */
  function save($filename, $type = false, $quality = false) {
    // If there's no type
    if (!$type) {
      // Set type
      $type = $this->type;
    }
    // If there's no quality
    if ($quality === false) {
      // Set quality
      $quality = $this->quality;
    }
    // Dump
    $this->canvas->save($filename, $type, array(
      'quality'=> $quality
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

  /**
   * Convert color to pixel
   */
  static function colorToPixel($color) {
    // If already pixel
    if ($color instanceof \Borla\Canvas\Properties\Pixel) {
      // Return
      return $color;
    }
    // Set r,g,b,a
    $red = 0;
    $green = 0;
    $blue = 0;
    $alpha = 0;
    // If not array
    if (!is_array($color)) {
      // Trim
      $color = trim($color, " \n\r\t#");
      // If less than 6
      if (strlen($color) < 6) {
        // Get first 3 chars and pad with 0
        $color = str_pad(substr($color, 0, 3), 3, '0');
        // New color
        $color = $color[0] . $color[0].
                 $color[1] . $color[1].
                 $color[2] . $color[2];
      }
      // If greater than 6
      if (strlen($color) > 6) {
        // Sub
        $color = substr($color, 0, 6);
      }
      // Convert to array
      $color = array(
        hexdec(substr($color, 0, 2)),
        hexdec(substr($color, 2, 2)),
        hexdec(substr($color, 4, 2)),
        0
      );
    }
    // If array
    if (is_array($color)) {
      // Set colors
      $red = isset($color[0]) ? $color[0] : 0;
      $green = isset($color[1]) ? $color[1] : 0;
      $blue = isset($color[2]) ? $color[2] : 0;
      $alpha = isset($color[3]) ? $color[3] : 0;
    }
    // Return pixel
    return new \Borla\Canvas\Properties\Pixel($red, $green, $blue, $alpha);
  }

}