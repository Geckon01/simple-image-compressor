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
    private const ALLOWED_IMAGE_FORMAT = "image/jpeg,image/png,image/gif,image/webp,image/bmp";
    private string $imageResourceUrl;
    private string $imageData;
    private string $imageType;

    private int $approxMinimumHeight = 90;
    private int $approxMinimumWidth = 90;

    private bool $exifLoaded = false;


    /**
     * SimpleImageCompressor constructor.
     * @param $url url or path from where to load file
     */
    private function __construct($url)
    {
        $this->imageResourceUrl = $url;
        $this->exifLoaded = in_array("exif", get_loaded_extensions());
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
        $this->imageType = "image";

        if($this->exifLoaded) {
            switch (exif_imagetype($this->imageResourceUrl))
            {
                case IMAGETYPE_GIF:
                    $this->imageType = 'image/gif';
                    break;
                case IMAGETYPE_JPEG:
                    $this->imageType = 'image/jpeg';
                    break;
                case IMAGETYPE_PNG:
                    $this->imageType = 'image/png';
                    break;
                case IMAGETYPE_BMP:
                    $this->imageType = 'image/bmp';
                    break;
                case IMAGETYPE_WEBP:
                    $this->imageType = 'image/webp';
                    break;
                case IMAGETYPE_ICO:
                    $this->imageType = 'image/ico';
                    break;
                default:
                    break;
            }
            return;
        } 

        // Fallback to bytes recognition
        trigger_error("simple-image-compressor: Exif extension not found. Image MIME type recognition may be inaccurate.");

        if (substr($this->imageData, 0, 2) === "\xFF\xD8") {
            $this->imageType = 'image/jpeg';
        }
        if (substr($this->imageData, 0, 3) === "\x89\x50\x4E") {
            $this->imageType = 'image/png';
        }
        if (substr($this->imageData, 0, 4) === "\x47\x49\x46\x38") {
            $this->imageType = 'image/gif';
        }
    }

    /**
     * Loading image from provided url. Whether it local file or internet resource
     */
    private function readImageToString(): void {
        $imageData = file_get_contents($this->imageResourceUrl);

        if($imageData === false)
            throw new \Exception("Cannot load image from provided resource: ".$this->imageResourceUrl);

        $this->imageData = $imageData;
    }

    /**
     * Resizes and compressing image.
     * Note that gif compression not supported
     * @param int $reductionPercent percent shows how much image resolution to original will be. The greater percent, the lower resolution
     * @param int $quality
     * @return CompressedImage
     */
    public function resizeAndCompress($reductionPercent = 5, $quality = 90): CompressedImage
    {
        $originImage = imagecreatefromstring($this->imageData);

        if($originImage === false)
            throw new \Exception("Can not read provided file");

        $width = imagesx($originImage);
        $height = imagesy($originImage);

        $totalPixelCount = $width * $height;
        $minimumPixelCount = $this->approxMinimumWidth * $this->approxMinimumHeight;
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
