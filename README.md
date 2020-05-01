# php-image
A library for doing simple things with images in PHP

# Examples

## Merging 2 images
```php
$imageA = Image::fromFile("a.png");
$imageB = Image::fromFile("b.png");

$result = $imageA->merge($imageB);

$result->save("result.png")
```
## Compare changes

### Draws a square around the changes
```php
$a = Image::fromFile("a.png");
$b = Image::fromFile("b.png");

// Number of parts that the image will be separated to be compared with
$cellsCount = 10;

// If should merge the compared images in the result image
$mergeImages = true;

$comparator = new ImageComparator(
  $a,
  $b,
  $cellsCount,
  $mergeImages
);

$result = $comparator
  // ->denoise(42) // Specify a custom denoise value
  ->denoiseStandardDeviation()
  ->drawChaningArea();
  ->getImage();

$result->save("result.png");
```

### Draws changes indicators
```php
...

$result = $comparator
  ->drawChangesIndicators();
  ->getImage();

$compression = 80;
$result->saveJpeg("result.jpg", $compression);
```

## Supported types
- PNG
- JPEG