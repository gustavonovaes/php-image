<?php

namespace GNovaes;

class ImageComparator
{
  private Image $image;

  private int $cellsCount;

  private array $mapColorDistance;

  public function __construct(
    Image $imageA,
    Image $imageB,
    int $cellsCount = 16,
    bool $merge = true
  ) {
    $this->image = $merge ? $imageA->merge($imageB) : $imageA;

    $this->cellsCount = $cellsCount;

    [$mapA, $mapB] = $this->generatePixelMap([$imageA, $imageB], $cellsCount);
    $this->mapColorDistance = $this->calculateColorDistance($mapA, $mapB);
  }

  public function denoise(float $limit)
  {
    foreach ($this->mapColorDistance as $y => $arr) {
      foreach ($arr as $x => $val) {
        if ($val < $limit) {
          $this->mapColorDistance[$y][$x] = 0;
        }
      }
    }

    return $this;
  }

  public function denoiseStandardDeviation(int $times = 1)
  {
    $average = $this->colorDistanceAverage($this->mapColorDistance, $this->cellsCount);
    $standardDeviation = $this->standardDeviation($this->mapColorDistance, $average, $this->cellsCount);

    $limit = $standardDeviation * $times;
    return $this->denoise($limit);
  }

  public function drawChangingArea(array $color = [0, 255, 0]): self
  {
    [
      'x' => $x,
      'y' => $y,
      'w' => $w,
      'h' => $h
    ] = $this->detectBoudingBox(
      $this->mapColorDistance,
      $this->image->getWidth(),
      $this->image->getHeight(),
      $this->cellsCount,
    );

    $this->image->drawRectangle(
      $x,
      $y,
      $w,
      $h,
      $color
    );

    return $this;
  }

  public function drawChangesIndicators(array $textColor = [0, 0, 0], array $boxColor = [0, 255, 0]): self
  {
    $imageResource = $this->image->getImage();

    $cellW = $this->image->getWidth() / $this->cellsCount;
    $cellH = $this->image->getHeight() / $this->cellsCount;

    $textColor = \imagecolorallocate($imageResource, ...$textColor);

    foreach ($this->mapColorDistance as $y => $_) {
      foreach ($_ as $x => $val) {
        if (!$val) {
          continue;
        }

        $color = \imagecolorallocate($imageResource, ...$boxColor);
        \imagerectangle(
          $imageResource,
          $x * $cellW,
          $y * $cellH,
          $x * $cellW + $cellW - 1,
          $y * $cellH + $cellH - 1,
          $color
        );

        \imagestring(
          $imageResource,
          2,
          $x * $cellW + 2,
          $y * $cellH + 1,
          round($val, 1),
          $textColor
        );
      }
    }

    return $this;
  }

  public function getImage(): Image
  {
    return $this->image;
  }

  private function generatePixelMap(array $images, int $cellsCount): array
  {
    $firstImage = current($images);
    $cellW = $firstImage->getWidth() / $cellsCount;
    $cellH = $firstImage->getHeight() / $cellsCount;

    $countImages = \count($images);

    $maps = [];
    for ($i = 0; $i < $countImages; $i++) {
      $maps[$i] = [];
      for ($y = 0; $y < $cellsCount; $y++) {
        $maps[$i][$y] = [];
        for ($x = 0; $x < $cellsCount; $x++) {
          $maps[$i][$y][$x] = [
            'r' => 0,
            'g' => 0,
            'b' => 0,
          ];
        }
      }
    }

    $imageResources = \array_map(fn ($image) => $image->getImage(), $images);

    for ($y = 0; $y < $cellsCount; $y++) {
      for ($x = 0; $x < $cellsCount; $x++) {
        for ($i = 0; $i < count($images); $i++) {
          $r = 0;
          $g = 0;
          $b = 0;

          for ($yi = 0; $yi < $cellH; $yi++) {
            for ($xi = 0; $xi < $cellW; $xi++) {
              $cellY = ($y * $cellH) + $yi;
              $cellX = ($x * $cellW) + $xi;

              $v = \imagecolorat($imageResources[$i], $cellX, $cellY);
              $r += ($v >> 16) & 0xFF;
              $g += ($v >> 8) & 0xFF;
              $b += $v & 0xFF;
            }
          }

          $maps[$i][$y][$x] = [
            'r' => $r / ($cellW * $cellH),
            'g' => $g / ($cellW * $cellH),
            'b' => $b / ($cellW * $cellH),
          ];
        }
      }
    }

    return $maps;
  }

  private function calculateColorDistance(array $mapA, array $mapB): array
  {
    foreach ($mapB as $y => $arrB) {
      foreach ($arrB as $x => $rgbB) {
        $rgbA = $mapA[$y][$x];

        $colorDistance = \sqrt(
          pow($rgbB['r'] - $rgbA['r'], 2) +
            pow($rgbB['g'] - $rgbA['g'], 2) +
            pow($rgbB['b'] - $rgbA['b'], 2)
        );

        $percentage = $colorDistance / \sqrt(pow(255, 2) * 3) * 100;
        $mapA[$y][$x] = $percentage;
      }
    }

    return $mapA;
  }

  private function colorDistanceAverage(array $map, int $cellsCount): int
  {
    $average = 0;
    foreach ($map as $arr) {
      foreach ($arr as $val) {
        $average += $val;
      }
    }

    return $average / pow($cellsCount, 2);
  }

  private function standardDeviation(array $map, int $avg, int $cellsCount)
  {
    $standardDeviation = 0;
    foreach ($map as $_) {
      foreach ($_ as $val) {
        $standardDeviation += pow($val - $avg, 2);
      }
    }

    return \sqrt($standardDeviation / (pow($cellsCount, 2) - 1));
  }

  private function detectBoudingBox(array $map, int $width, int $height, int $cellsCount)
  {
    $startX = $cellsCount;
    $startY = $cellsCount;

    $endX = 0;
    $endY = 0;

    foreach ($map as $y => $_) {
      foreach ($_ as $x => $val) {
        if ($val === 0) {
          continue;
        }

        if ($x < $startX) $startX = $x;
        if ($x > $endX) $endX = $x;

        if ($y < $startY) $startY = $y;
        if ($y > $endY) $endY = $y;
      }
    }

    if ($startX > $endX) {
      return null;
    }

    $x = ($startX / $cellsCount) * $width;
    $width = ((($endX + 1) / $cellsCount) * $width) - $x;
    $y = ($startY / $cellsCount) * $height;
    $height = ((($endY + 1) / $cellsCount) * $height) - $y;

    return [
      "x" => $x,
      "y" => $y,
      "w" => $width,
      "h" => $height
    ];
  }
}
