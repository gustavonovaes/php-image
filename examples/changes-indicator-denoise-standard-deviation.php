<?php

require __DIR__ . '/../vendor/autoload.php';

use GNovaes\Image;
use GNovaes\ImageComparator;

$imageA = Image::fromFile(__DIR__ . '/a.png');
$imageB = Image::fromFile(__DIR__ . '/b.png');
$imageB->resize($imageA->getWidth(), $imageA->getHeight());

$cellsCount = 32;
$mergeImages = true;

header('Content-type: image/png');

echo (new ImageComparator($imageA, $imageB, $cellsCount, $mergeImages))
  ->denoiseStandardDeviation()
  ->drawChangesIndicators()
  ->getImage()
  ->getData();