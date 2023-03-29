<?php
/**
 * This is a simple PHP image compressor which allows you to compress any image easily on the fly.
 * This lib actually compressing and resizing image saving its original proportion
 * Geckon01(c) 2023
 * Class SimpleImageCompressor
 */

namespace geckon01\SimpleImageCompressor;

/**
 * Class SimpleImageCompressor
 * @package geckon01\SimpleImageCompressor
 */
class SimpleImageCompressor
{
    private const ALLOWED_IMAGE_FORMAT = "image/jpeg,image/png,image/gif";
    private string $imageResourceUrl;
    private string $imageData;
    private string $imageType;

    private int $approxMinimumHeight = 90;
    private int $approxMinimumWidth = 90;

    /**
     * SimpleImageCompressor constructor.
     * @param $url
     */
    private function __construct($url)
    {
        $this->imageResourceUrl = $url;
    }

    /**
     * Returns current loaded image type
     * @return string
     */
    public function getImageType(): string
    {
        return $this->imageType;
    }

    /**
     * @return int
     */
    public function getApproxMinimumHeight(): int
    {
        return $this->approxMinimumHeight;
    }

    /**
     * @return int
     */
    public function getApproxMinimumWidth(): int
    {
        return $this->approxMinimumWidth;
    }

    /**
     * @param int $approxMinimumHeight
     */
    public function setApproxMinimumHeight(int $approxMinimumHeight): SimpleImageCompressor
    {
        $this->approxMinimumHeight = $approxMinimumHeight;
        return $this;
    }

    /**
     * @param int $approxMinimumWidth
     */
    public function setApproxMinimumWidth(int $approxMinimumWidth): SimpleImageCompressor
    {
        $this->approxMinimumWidth = $approxMinimumWidth;
        return $this;
    }

    /**
     * Determine current loaded image type
     */
    private function loadImageType() {
        $filetype = '';

        if (substr($this->imageData, 0, 2) === "\xFF\xD8") {
            $filetype = 'image/jpeg';
        } elseif (substr($this->imageData, 0, 3) === "\x89\x50\x4E") {
            $filetype = 'image/png';
        } elseif (substr($this->imageData, 0, 4) === "\x47\x49\x46\x38") {
            $filetype = 'image/gif';
        }

        $this->imageType = $filetype;
    }

    /**
     * Loading image from provided url. Whether it local file or internet resource
     */
    private function readImageToString(): void {
        $imageData = file_get_contents($this->imageResourceUrl);
        $this->imageData = $imageData;
    }

    /**
     * Resizes and compressing image.
     * Note that gif compression not supported
     * @param int $resolutionReductionPercent percent shows how much image resolution to original will be. The greater percent, the lower resolution
     * @param int $quality
     * @return CompressedImage
     */
    public function resizeAndCompress($resolutionReductionPercent = 5, $quality = 90): CompressedImage
    {
        $im = imagecreatefromstring($this->imageData);
        $width = imagesx($im);
        $height = imagesy($im);

        $totalPixelCount = $width * $height;
        $minimumPixelCount = $this->approxMinimumWidth * $this->approxMinimumHeight;
        $maximumResolutionReductionPercent = round(abs(100 - ($minimumPixelCount / $totalPixelCount * 100)));

        //Due to saving proportion we can't guarantee that width and height be equals max and min
        //As example, if we have original image 1920*1080 which we want to get 50% of original resolution
        //If we want to save 16*9 aspect ration it must be 960*540
        //So, we override $maximumResolutionReductionPercent to value which satisfy origin aspect ratio
        if($maximumResolutionReductionPercent < $resolutionReductionPercent)
            $resolutionReductionPercent = $maximumResolutionReductionPercent;

        $newWidth = round($width - ($width * $resolutionReductionPercent) / 100);
        $newHeight = round($height - ($height * $resolutionReductionPercent) / 100);

        $thumb = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresized($thumb, $im, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        return new CompressedImage($quality, $this->imageType,$thumb);
    }

    /**
     * Initalize lib from provided image file path
     * @param string $urlImageResource image resource path. Can be local file or internet URL
     * @return SimpleImageCompressor
     * @throws \Exception
     */
    public static function load(string $urlImageResource): SimpleImageCompressor
    {
        $compressorObject = new SimpleImageCompressor($urlImageResource);
        $compressorObject->readImageToString();
        $compressorObject->loadImageType();

        if(!str_contains(self::ALLOWED_IMAGE_FORMAT, $compressorObject->getImageType())
            || $compressorObject->getImageType() === "")
            throw new \Exception("Provided file resource must contain one of image mime types: ".self::ALLOWED_IMAGE_FORMAT);

        return $compressorObject;
    }
}