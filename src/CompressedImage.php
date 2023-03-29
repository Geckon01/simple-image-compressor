<?php


namespace geckon01\SimpleImageCompressor;

/**
 * Class CompressedImage
 * @package geckon01\SimpleImageCompressor
 */
class CompressedImage
{
    private int $qualityRate;
    private string $imageType;
    private \GdImage $imageObject;

    /**
     * CompressedImage constructor.
     * @param int $qualityRate
     * @param string $imageType
     * @param \GdImage $imageObject
     */
    public function __construct(int $qualityRate, string $imageType, \GdImage $imageObject)
    {
        $this->qualityRate = $qualityRate;
        $this->imageType = $imageType;
        $this->imageObject = $imageObject;
    }

    /**
     * Mapping one value to another
     * @param int $value value
     * @param int $fromMin origin value min
     * @param int $fromMax origin value max
     * @param int $toMin target value min
     * @param int $toMax target value max
     * @return false|float
     */
    private function mapValue(int $value, int $fromMin, int $fromMax, int $toMin, int $toMax) {
        $fromRange = $fromMax - $fromMin;
        $toRange = $toMax - $toMin;

        $scaled = ($value - $fromMin) / $fromRange;

        return ceil($toMin + ($scaled * $toRange));
    }

    /**
     * Outputs current image as base64 string
     * @return string
     */
    public function toBase64(): string
    {
        ob_start();
        switch ($this->imageType)
        {
            case "image/jpeg":
                imagejpeg($this->imageObject, null, $this->qualityRate);
                break;
            case "image/png":
                imagepng(   $this->imageObject, null, self::mapValue($this->qualityRate, 0, 100, 0, 9));
                break;
            case "image/gif":
                imagegif($this->imageObject);
                break;
        }
        $base64_output = ob_get_contents();
        ob_end_clean();

        $base64_output = base64_encode($base64_output);
        return $base64_output;
    }

    /** Outputs current image to file with provided path
     * @param $filePath string new file name without extension
     */
    public function toFile($filePath): void
    {
        switch ($this->imageType)
        {
            case "image/jpeg":
                imagejpeg($this->imageObject, $filePath.".jpg", $this->qualityRate);
                break;
            case "image/png":
                imagepng(   $this->imageObject, $filePath.".png", self::mapValue($this->qualityRate, 0, 100, 0, 9));
                break;
            case "image/gif":
                imagegif($this->imageObject, $filePath.".gif");
                break;
        }
    }

    /**
     * Outputs current image to GdImage
     * @return \GdImage
     */
    public function toGdImage(): \GdImage
    {
        return $this->imageObject;
    }
}