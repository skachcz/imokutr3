<?php

namespace SkachCz\Imokutr3\Data;

/**
 * @package SkachCz\Imokutr3\Data
 * @author  Vladimir Skach
 */
class ThumbnailInfo
{
    public string $url;
    public int $width;
    public int $height;

    public function __construct(string $url, int $width, int $height)
    {
        $this->url = $url;
        $this->width = $width;
        $this->height = $height;
    }
}
