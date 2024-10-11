<?php

namespace SkachCz\Imokutr3\Data;

/**
 * @package SkachCz\Imokutr3\Data
 * @author  Vladimir Skach
 */
class ImageInfo
{
    public int $width;
    public int $height;

    public function __construct(int $width, int $height)
    {
        $this->width = $width;
        $this->height = $height;
    }
}
