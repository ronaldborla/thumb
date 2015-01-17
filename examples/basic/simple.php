<?php

/**
 * Simple thumbnails
 */

// Require autoload
require('../../vendor/autoload.php');

// Image to load
$image = dirname(dirname(__FILE__)).'/assets/images/nature.jpg';
// Create thumb and dump
\Borla\Thumb\Thumb::create($image, array(
  // Set dimensions
  'width'=> 200,
  'height'=> 160
  // Dump as JPG
))->dump();