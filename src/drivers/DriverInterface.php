<?php


namespace geckon01\SimpleImageCompressor\Drivers;

use geckon01\SimpleImageCompressor\CompressedImage;
use geckon01\SimpleImageCompressor\SimpleImageCompressor;

interface DriverInterface
{
    function resize(int $reductionPercent, int $quality, int $approxMinimumWidth, int $approxMinimumHeight, string $imageType): CompressedImage;
    function load(string $path): DriverInterface;
    function getImageBinary(): string;
}