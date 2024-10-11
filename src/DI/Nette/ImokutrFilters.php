<?php
namespace SkachCz\Imokutr3\DI\Nette;

use SkachCz\Imokutr3\ImokutrConfig;
use SkachCz\Imokutr3\Image;
use SkachCz\Imokutr3\Thumbnail;

/**
 * @package SkachCz\Imokutr\Nette
 * @author Vladimir Skach
 */
class ImokutrFilters {

    /** @var ImokutrConfig */
    public $config;

    public function __construct(ImokutrConfig $config)
	{
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function imoUrl(string $path, int $width, int $height, string $fixedDimension = 'w', int $cropType = Image::CROP_CENTER, bool $force = false) {

        switch($fixedDimension) {

            case 'c':
                $fixed = Image::DIM_CROP;
                break;

            case 'h':
                $fixed = Image::DIM_HEIGHT;
                break;

            default:
                $fixed = Image::DIM_WIDTH;
        }

        $image = new Image($this->config->originalRootPath, $path, $this->config->defaultImageRelativePath);
        $thumbnail = new Thumbnail($this->config, $image);

        $thumbnail->setResize($width, $height, $fixed, $cropType);

        $thumbnail->processImage($force);

        return $thumbnail->getThumbnailUrl();

    }


}