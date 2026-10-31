<?php

declare(strict_types=1);

namespace SkachCz\Imokutr3\DI\Nette;

use SkachCz\Imokutr3\Image;
use SkachCz\Imokutr3\Imokutr;
use SkachCz\Imokutr3\ImokutrConfig;

/**
 * @author Vladimir Skach
 */
class ImokutrFilters
{
    public function __construct(
        public readonly ImokutrConfig $config,
    ) {
    }

    public function imoUrl(
        string $path,
        int $width,
        int $height,
        string $fixedDimension = 'w',
        int $cropType = Image::CROP_CENTER,
        bool $force = false,
    ): string {
        $imokutr = new Imokutr($this->config);

        return $imokutr->getThumbnail($path, $width, $height, $fixedDimension, $cropType, $force)->url;
    }
}
