<?php

require __DIR__ . '/../vendor/autoload.php';

use GNovaes\Image;
use GNovaes\ImageComparator;

$imageA = Image::fromFile(__DIR__ . '/a.png');
$imageB = Image::fromFile(__DIR__ . '/b.png');
$imageB->resize($imageA->getWidth(), $imageA->getHeight());

header('Content-type: image/png');

echo $imageA->merge($imageB)->getData();