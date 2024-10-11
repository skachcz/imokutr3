<?php

namespace SkachCz\Imokutr3\Data;

/**
 * @package SkachCz\Imokutr3\Data
 * @author  Vladimir Skach
 */
class CropInfo
{
    public int $cropX;
    public int $cropY;
    public int $width;
    public int $height;

    public function __construct(int $cropX, int $cropY, int $width, int $height)
    {
        $this->cropX = $cropX;
        $this->cropY = $cropY;
        $this->width = $width;
        $this->height = $height;
    }
}
