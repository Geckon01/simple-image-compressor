<?php


namespace geckon01\SimpleImageCompressor\Drivers;


use geckon01\SimpleImageCompressor\CompressedImage;

class GdDriver implements DriverInterface
{
    private string $imageData;

    function resize(int $reductionPercent, int $quality, int $approxMinimumWidth, int $approxMinimumHeight, string $imageType): CompressedImage
    {
        $originImage = imagecreatefromstring($this->imageData);

        if($originImage === false)
            throw new \Exception("Can not read provided file");

        $width = imagesx($originImage);
        $height = imagesy($originImage);

        $totalPixelCount = $width * $height;
        $minimumPixelCount = $approxMinimumWidth * $approxMinimumHeight;
        $maxReductionPercent = round(abs(100 - ($minimumPixelCount / $totalPixelCount * 100)));

        // Due to saving proportion we can't guarantee that width and height be equals max and min
        // As example, if we have original image 1920*1080 which we want to get 50% of original resolution
        // If we want to save 16*9 aspect ration it must be 960*540
        // So, we override $maxReductionPercent to value which satisfy origin aspect ratio
        if($maxReductionPercent < $reductionPercent)
            $reductionPercent = $maxReductionPercent;

        $newWidth = round($width - ($width * $reductionPercent) / 100);
        $newHeight = round($height - ($height * $reductionPercent) / 100);

        $thumb = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresized($thumb, $originImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        return new CompressedImage($quality, $imageType, $thumb);
    }

    function load(string $path): DriverInterface
    {
        $this->imageData = file_get_contents($path);

        if($this->imageData === false)
            throw new \Exception("Cannot load image from provided resource: ".$path);

        return $this;
    }

    function getImageBinary(): string
    {
        if($this->imageData == null)
            throw new \Exception("Load image before getting its binary.");

        return $this->imageData;
    }
}