

# SimpleImageCompressor


![Version](https://img.shields.io/packagist/v/geckon01/simple-image-compressor)
![Lecense](https://img.shields.io/badge/license-MIT-green)
![Downloads](https://img.shields.io/packagist/dt/geckon01/simple-image-compressor)
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/d773335a657d467faaa0ebb12bc2abe1)](https://app.codacy.com/gh/Geckon01/simple-image-compressor/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)

**SimpleImageCompressor** - is a tiny simple PHP image resizer lib which allows you to resize and compress any image easily on the fly.

## Installation

### With composer
```bash
composer require "geckon01/simple-image-compressor"
```

### Without composer

 1. Download latest release [here](https://github.com/Geckon01/simple-image-compressor/releases).
 2. Unpack the archive to your project directory.
 3. Include the library files:

```php
require "src/SimpleImageCompressor.php";  
require "src/CompressedImage.php";
use geckon01\SimpleImageCompressor\SimpleImageCompressor;
```

## Usage

Resize and compress an image:
```php
$resolutionTargetPercent = 50;
$targetQuality = 50;
$compressor = SimpleImageCompressor::load("image.png");
$compressedImage = $compressor->resizeAndCompress($resolutionTargetPercent, $targetQuality);
$compressedImage->toFile("image");
```
load method supports loading from local file, or you can specify any valid URL image link like this:
```php
$compressor = SimpleImageCompressor::load("https://example.com/image.jpg");
```
Method chaining is supported:
```php
SimpleImageCompressor::load("image.png")
	->resizeAndCompress(50, 50)
	->toFile("image");
```
> Note: File extensions are automatically added. Use toFile("filename") without extension.

### Output format
You can specify output format. Supported output fomats are:

```php 
$compressedImage->toFile("image");
```
```php 
$compressedImage->toBase64();
```
```php 
$compressedImage->toGdImage();
```
### Size Constraints
Set approximate minimum dimensions (aspect ratio preserved): 
```php 
$compressor->setApproxMinimumHeight(500);  
$compressor->setApproxMinimumWidth(500);
```
> Note actual dimensions may differ due to aspect ratio preservation.
Example: 1920×1080 image reduced to 50% becomes 960×540 (maintaining 16:9).

## License

This software is licensed under the MIT License. [View the license](LICENSE.md).
