<?php

require __DIR__ . '/../vendor/autoload.php';

use GNovaes\Image;
use GNovaes\ImageComparator;

$imageA = Image::fromFile(__DIR__ . '/a.png');
$imageB = Image::fromFile(__DIR__ . '/b.png');

$compression = 80;
$imageA->merge($imageB)
  ->saveJpeg(__DIR__ . '/merge.jpeg', $compression);

// Number of parts that the image will be separated to be compared with
$cellsCount = 9;

// If should merge the compared images in the result image
$mergeImages = true;

(new ImageComparator($imageA, $imageB, $cellsCount, $mergeImages))
  ->denoiseStandardDeviation()
  ->drawChangesIndicators()
  ->getImage()
  ->save(__DIR__ . '/changes-indicator-denoise-standard-deviation.png');

(new ImageComparator($imageA, $imageB, $cellsCount, $mergeImages))
  ->drawChangesIndicators()
  ->getImage()
  ->save(__DIR__ . '/changes-indicator-without-denoise.png');

(new ImageComparator($imageA, $imageB, $cellsCount, false))
  ->denoise(50) // Custom denoise value
  ->drawChangingArea([255, 0, 0]) // RGB red
  ->getImage()
  ->save(__DIR__ . '/changing-area-with-custom-denoise.png');

(new ImageComparator($imageA, $imageB, $cellsCount, true))
  ->denoiseStandardDeviation()
  ->drawChangingArea()
  ->getImage()
  ->save(__DIR__ . '/changing-area-denoise-standard-deviation.png');

$image1 = Image::fromFile(__DIR__ . '/security-cam-1.png');
$image2 = Image::fromFile(__DIR__ . '/security-cam-2.png');
$image2->resize($image1->getWidth(), $image1->getHeight());

(new ImageComparator($image1, $image2, 16, true))
  ->denoiseStandardDeviation()
  ->drawChangesIndicators([0, 0, 255], [255, 0, 0])
  ->getImage()
  ->save(__DIR__ . '/real-security-cam-changes-indicator.png');

(new ImageComparator($image1, $image2, 16, true))
  ->denoiseStandardDeviation()
  ->drawChangingArea([255, 0, 0])
  ->getImage()
  ->save(__DIR__ . '/real-security-cam-changing-area.png');