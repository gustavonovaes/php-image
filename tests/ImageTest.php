<?php

declare(strict_types=1);

namespace Test\GNovaes;

use GNovaes\Image;
use PHPUnit\Framework\TestCase;

class ImageTest extends TestCase
{
  public function testCanConstruct()
  {
    $imageResource = \imagecreatetruecolor(100, 100);
    $image = new Image($imageResource);
    $this->assertSame($imageResource, $image->getImage());
  }

  public function testImageResourceIsReleasedWhenClassIsDestroyed()
  {
    $imageResource = \imagecreatetruecolor(100, 100);
    $this->assertTrue(\is_resource($imageResource));

    $image = new \GNovaes\Image($imageResource);
    $image->__destruct();

    $this->assertFalse(\is_resource($imageResource));
  }

  public function testCanCreateFromPngFile()
  {
    $imageResource = \imagecreatetruecolor(100, 100);

    $filePath = \tempnam(\sys_get_temp_dir(), self::class);
    \imagepng($imageResource, $filePath);

    $image = Image::fromFile($filePath);

    $this->assertTrue(\is_resource($image->getImage()));
  }

  public function testCanCreateFromJpegFile()
  {
    $imageResource = \imagecreatetruecolor(100, 100);

    $filePath = \tempnam(\sys_get_temp_dir(), self::class);
    \imagejpeg($imageResource, $filePath);

    $image = Image::fromFile($filePath);

    $this->assertTrue(\is_resource($image->getImage()));
  }

  public function testFailWhenCreateFromAInvalidImageFile()
  {
    $filePath = \tempnam(\sys_get_temp_dir(), self::class);
    \file_put_contents($filePath, 0x2A);

    $this->expectException("InvalidArgumentException");
    Image::fromFile($filePath);
  }

  public function testFailWhenCreateFromAUnsupportedImageType()
  {
    $this->markTestIncomplete();
  }

  public function testCanGetWidth()
  {
    $width = 50;
    $image = $this->factoryImage($width, 100);
    $this->assertEquals($image->getWidth(), $width);
  }

  public function testCanGetHeight()
  {
    $height = 50;
    $image = $this->factoryImage(100, $height);
    $this->assertEquals($image->getHeight(), $height);
  }

  public function testCanCrop()
  {
    $image = $this->factoryImage(100, 100);

    $colorRed = [255, 0, 0];
    $colorGreen = [0, 255, 0];

    $image->drawRectangle(0, 0, 1, 1, $colorRed);
    $image->drawRectangle(5, 5, 1, 1, $colorGreen);

    $this->assertEquals($image->getColorAt(0, 0), $colorRed);

    $image->crop(5, 5, 25, 50);

    $this->assertEquals($image->getColorAt(0, 0), $colorGreen);
    $this->assertEquals(25, $image->getWidth());
    $this->assertEquals(50, $image->getHeight());
  }

  public function testCanResizeToWidth()
  {
    $image = $this->factoryImage(100, 50);
    $image->resizeToWidth(50);
    $this->assertEquals($image->getHeight(), 25);
  }

  public function testCanResizeToHeight()
  {
    $image = $this->factoryImage(50, 100);
    $image->resizeToHeight(50);
    $this->assertEquals($image->getWidth(), 25);
  }

  public function testCanResize()
  {
    $image = $this->factoryImage(100, 100);
    $image->resize(50, 50);
    $this->assertEquals($image->getWidth(), 50);
    $this->assertEquals($image->getHeight(), 50);
  }

  public function testCanDrawRectangle()
  {
    $image = $this->factoryImage(3, 3);

    $cornerColorsBefore = [
      $image->getColorAt(0, 0),
      $image->getColorAt(2, 0),
      $image->getColorAt(0, 2),
      $image->getColorAt(2, 2),
    ];

    $image->drawRectangle(0, 0, 3, 3, [255, 0, 0]);

    $cornerColorsAfter = [
      $image->getColorAt(0, 0),
      $image->getColorAt(2, 0),
      $image->getColorAt(0, 2),
      $image->getColorAt(2, 2),
    ];

    $this->assertNotEquals($cornerColorsAfter, $cornerColorsBefore);
    $this->assertEquals($cornerColorsAfter, [
      [255, 0, 0],
      [255, 0, 0],
      [255, 0, 0],
      [255, 0, 0],
    ]);
    $this->assertEquals($image->getColorAt(1, 1), [0, 0, 0]);
  }

  public function testCanDrawRectangleFilled()
  {
    $image = $this->factoryImage(3, 3);

    $image->drawRectangle(0, 0, 3, 3, [255, 0, 0], true);

    for ($i = 0; $i < 3; $i++) {
      $this->assertEquals($image->getColorAt($i, $i), [255, 0, 0]);
    }
  }

  public function testCanMerge()
  {
    $this->markTestIncomplete();
  }

  public function testCanSave()
  {
    $this->markTestIncomplete();
  }

  public function testCanSaveJpeg()
  {
    $this->markTestIncomplete();
  }

  private function factoryImage(int $width = 100, int $height = 100)
  {
    $imageResource = \imagecreatetruecolor($width, $height);
    return new Image($imageResource);
  }
}
