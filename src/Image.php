<?php

namespace GNovaes;

class Image
{
  /**
   * @var resource
   */
  private $image;

  /**
   * @param resource $image
   */
  public function __construct($image)
  {
    $this->image = $image;
  }

  public function __destruct()
  {
    if (\is_resource($this->image)) {
      \imagedestroy($this->image);
    }
  }

  /**
   * Undocumented function
   * @param string $filePath
   *
   * @return self
   * 
   * @throws \InvalidArgumentException When a invalid image is passed in $filePath
   * @throws \InvalidArgumentException When a not supported image type file is passed
   */
  public static function fromFile(string $filePath): self
  {
    $fileInfo = @\getimagesize($filePath);
    if (false === $fileInfo) {
      throw new \InvalidArgumentException("Can't get type of the image");
    }

    [
      2 => $fileType,
    ] = $fileInfo;

    if ($fileType == \IMAGETYPE_PNG) {
      $image = \imagecreatefrompng($filePath);
    } else if ($fileType == \IMAGETYPE_JPEG) {
      $image = \imagecreatefromjpeg($filePath);
    } else {
      throw new \InvalidArgumentException("File type not supported");
    }

    return new self($image);
  }

  public function getWidth(): int
  {
    return \imagesx($this->image);
  }

  public function getHeight(): int
  {
    return \imagesy($this->image);
  }

  public function getImage()
  {
    return $this->image;
  }

  public function crop(int $x, int $y, $width, $height)
  {
    $newImage = \imagecreatetruecolor($width, $height);

    \imagecopyresampled($newImage, $this->image, 0, 0, $x, $y, $width, $height, $width, $height);

    \imagedestroy($this->image);
    $this->image = $newImage;

    return $this;
  }

  public function resizeToWidth(int $width)
  {
    $ratio = ($width / $this->getWidth());

    $height = $this->getHeight() * $ratio;

    return $this->resize($width, $height);
  }

  public function resizeToHeight(int $height)
  {
    $ratio = ($height / $this->getHeight());

    $width = $this->getWidth() * $ratio;

    return $this->resize($width, $height);
  }

  public function resize(int $width, int $height)
  {
    $newImage = \imagecreatetruecolor($width, $height);

    \imagecopyresampled($newImage, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());

    \imagedestroy($this->image);
    $this->image = $newImage;

    return $this;
  }

  public function drawRectangle(int $x, int $y, int $w, int $h, array $rgb, bool $fill = false)
  {
    $color = \imagecolorallocate($this->image, ...$rgb);

    if ($fill) {
      for ($i = 0; $i <= $w / 2; $i++) {
        for ($j = 0; $j <= $h / 2; $j++) {
          \imagerectangle(
            $this->image,
            $x + $i,
            $y + $j,
            $x - $i + $w,
            $y - $j + $h,
            $color
          );
        }
      }
    } else {
      \imagerectangle(
        $this->image,
        $x,
        $y,
        $x + $w - 1,
        $y + $h - 1,
        $color
      );
    }

    return $this;
  }

  public function getColorAt(int $x, int $y)
  {
    $v =  \imagecolorat($this->image, $x, $y);

    return [
      ($v >> 16) & 0xFF,
      ($v >> 8) & 0xFF,
      $v & 0xFF
    ];
  }

  public function merge(Image $otherImage)
  {
    $width = $this->getWidth();
    $height = $this->getHeight();

    $newImage = \imagecreatetruecolor($width, $height);

    $imageResourceA = $this->image;
    $imageResourceB = $otherImage->getImage();

    for ($y = 0; $y < $height; $y++) {
      for ($x = 0; $x < $width; $x++) {
        $colorA = \imagecolorat($imageResourceA, $x, $y);
        $rA = ($colorA >> 16) & 0xFF;
        $gA = ($colorA >> 8) & 0xFF;
        $bA = $colorA & 0xFF;

        $colorB = \imagecolorat($imageResourceB, $x, $y);
        $rB = ($colorB >> 16) & 0xFF;
        $gB = ($colorB >> 8) & 0xFF;
        $bB = $colorB & 0xFF;

        $color = \imagecolorallocate(
          $newImage,
          ($rA + $rB) / 2,
          ($gA + $gB) / 2,
          ($bA + $bB) / 2
        );

        \imagesetpixel($newImage, $x, $y, $color);
      }
    }

    return new self($newImage);
  }

  public function save(string $filePath)
  {
    return \imagepng($this->image, $filePath);
  }

  public function saveJpeg(string $filePath, int $compression = 80)
  {
    return \imagejpeg($this->image, $filePath, $compression);
  }

  public function base64()
  {
    \ob_start();
    \imagepng($this->image, null);
    $content = \ob_get_clean();
    
    return 'data:image/png;base64,' . \base64_encode($content);
  }

  public function base64Jpeg(int $compression = 80)
  {
    \ob_start();
    \imagejpeg($this->image, null, $compression);
    $content = \ob_get_clean();

    return 'data:image/jpeg;base64,' . \base64_encode($content);
  }
}
