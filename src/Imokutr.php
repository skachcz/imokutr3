<?php

declare(strict_types=1);

namespace SkachCz\Imokutr3;

use SkachCz\Imokutr3\Data\ThumbnailInfo;
use SkachCz\Imokutr3\Exception\ImokutrWrongMacroParameterException;

/**
 * Main class
 *
 * @author Vladimir Skach
 */
class Imokutr
{
    public function __construct(
        public readonly ImokutrConfig $config,
    ) {
    }

    public function getConfig(): ImokutrConfig
    {
        return $this->config;
    }

    /**
     * Returns thumbnail url
     */
    public function getThumbnailUrl(
        string $path,
        int $width,
        int $height,
        int $fixed = Image::DIM_WIDTH,
        int $cropType = Image::CROP_CENTER,
        bool $force = false,
    ): string {
        $image = new Image($this->config->originalRootPath, $path, $this->config->defaultImageRelativePath);
        $thumbnail = new Thumbnail($this->config, $image);
        $thumbnail->setResize($width, $height, $fixed, $cropType);
        $thumbnail->processImage($force);

        return $thumbnail->getThumbnailUrl();
    }

    /**
     * Returns thumbnail array
     */
    public function getThumbnail(
        string $path,
        int $width,
        int $height,
        string $fixedPar = 'w',
        int $cropType = Image::CROP_CENTER,
        bool $force = false,
    ): ThumbnailInfo {
        $image = new Image($this->config->originalRootPath, $path, $this->config->defaultImageRelativePath);
        $thumbnail = new Thumbnail($this->config, $image);
        $thumbnail->setResize($width, $height, Image::dimensionFromParameter($fixedPar), $cropType);

        return $thumbnail->processImage($force);
    }

    /**
     * helper for Nette macro
     */
    public function macroThumbInterface(
        ?string $path = null,
        ?int $width = null,
        ?int $height = null,
        string $fixedPar = 'w',
        int $cropType = Image::CROP_CENTER,
        bool $force = false,
    ): ThumbnailInfo {
        if ($this->config->defaultImageRelativePath === '' && $path === null) {
            throw new ImokutrWrongMacroParameterException('1 (path)', 'valid relative path to the image');
        }

        if ($width === null || $width <= 0) {
            throw new ImokutrWrongMacroParameterException('2 (width)', 'integer higher than 0');
        }

        if ($height === null || $height <= 0) {
            throw new ImokutrWrongMacroParameterException('2 (height)', 'integer higher than 0');
        }

        if (!in_array($fixedPar, ['w', 'h', 'c'], true)) {
            throw new ImokutrWrongMacroParameterException('3 (resize type)', '"w", "h" or "c"');
        }

        if ($cropType < Image::CROP_CENTER || $cropType > Image::CROP_LEFT_CENTER) {
            throw new ImokutrWrongMacroParameterException('4 (crop type)', 'integer between 0 - 8');
        }

        return $this->getThumbnail(
            $path ?? '',
            $width,
            $height,
            $fixedPar,
            $cropType,
            $force,
        );
    }
}
