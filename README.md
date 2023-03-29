

# SimpleImageCompressor


![Version](https://img.shields.io/packagist/v/geckon01/simple-image-compressor)
![Lecense](https://img.shields.io/badge/license-MIT-green)
![Downloads](https://img.shields.io/packagist/dt/geckon01/simple-image-compressor)
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/d773335a657d467faaa0ebb12bc2abe1)](https://app.codacy.com/gh/Geckon01/simple-image-compressor/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)

**SimpleImageCompressor** - is a tiny simple PHP image compressor lib which allows you to compress any image easily on the fly.

## Installation

### With composer
```bash
composer require "geckon01/simple-image-compressor"
```

### Without composer

 1.  Download latest release [here](https://github.com/Geckon01/simple-image-compressor/releases).
 2. Unpack archive to any folder of your project you wish.
 3. Load main lib files:

```php
require "src/SimpleImageCompressor.php";  
require "src/CompressedImage.php";
use geckon01\SimpleImageCompressor\SimpleImageCompressor;
```

## Usage

To resize and compress your image you can use next code:
```php
$resulutionTargetPercent = 50;
$targetQuality = 50;
$compressor = SimpleImageCompressor::load("image.png");
$compressedImage = $compressor->resizeAndCompress($resulutionTargetPercent, $targetQuality);
$compressedImage->toFile("image");
```
load method supports loading from local file, or you can specify any valid URL image link like this:
```php
$compressor = SimpleImageCompressor::load("https://example.com/image.jpg");
```
This lib support chaining, so you can do something like this:
```php
SimpleImageCompressor::load("image.png")
	->resizeAndCompress(50, 50)
	->toFile("image");
```
> Note that you don't need to specify file extension. The lib will save
> file with proper one automatically. 

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
### Max/min height/width
Also you can set approximate minumum and maximum image size. 
```php 
$compressor->setApproxMinimumHeight(500);  
$compressor->setApproxMinimumWidth(500);
```
> Note that Due to saving proportion lib can't guarantee that width and height be equals max and min . 
As example, if we have original image 1920x1080 which we want to get 50% of original resolution  and save original 16x9 aspect ration the reduced image must be 960x540.

## License

This software is licensed under the MIT License. [View the license](LICENSE.md).