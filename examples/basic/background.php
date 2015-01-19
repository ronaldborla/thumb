<?php

/**
 * Simple thumbnails
 */

// Require autoload
require('../../vendor/autoload.php');

// Image to load
$image = dirname(dirname(__FILE__)).'/assets/images/logo.png';
// Create thumb and dump
\Borla\Thumb\Thumb::create($image, array(
  // Set dimensions
  'width'=> 90,
  'height'=> 20,
  // Set type as png
  'type'=> 'png',
  // Set background
  'background'=> '#aa0000'
  // Dump as PNG
))->dump();