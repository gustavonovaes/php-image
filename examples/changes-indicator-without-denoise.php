<?php

require __DIR__ . '/../vendor/autoload.php';

use GNovaes\Image;
use GNovaes\ImageComparator;

$imageA = Image::fromFile(__DIR__ . '/a.png');
$imageB = Image::fromFile(__DIR__ . '/b.png');
$imageB->resize($imageA->getWidth(), $imageA->getHeight());

$cellsCount = 10;
$mergeImages = true;

header('Content-type: image/png');

echo (new ImageComparator($imageA, $imageB, $cellsCount, $mergeImages))
  ->drawChangesIndicators()
  ->getImage()
  ->getData();