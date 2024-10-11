<?php
namespace SkachCz\Imokutr3;

/**
 * @package SkachCz\Imokutr3
 * @author  Vladimir Skach
 */
class ImokutrConfig
{

    public string $originalRootPath;
    public string $thumbsRootPath;
    public string $thumbsRootRelativePath;
    public string $defaultImageRelativePath;
    public int $qualityJpeg = 75;
    public int $qualityPng = 6;

    public function setConfig(
        string $originalRootPath,
        string $thumbsRootPath,
        string $thumbsRootRelativePath,
        string $defaultImageRelativePath,
        int $qualityJpeg = 75,
        int $qualityPng = 6
    ) {

        $this->originalRootPath = $originalRootPath;
        $this->thumbsRootPath = $thumbsRootPath;
        $this->thumbsRootRelativePath = $thumbsRootRelativePath;
        $this->defaultImageRelativePath = $defaultImageRelativePath;
        $this->qualityJpeg = $qualityJpeg;
        $this->qualityPng = $qualityPng;
    }
    public function setFromArray(array $parameters): void
    {
            $this->originalRootPath = $parameters['originalRootPath'];
            $this->thumbsRootPath = $parameters['thumbsRootPath'];
            $this->thumbsRootRelativePath = $parameters['thumbsRootRelativePath'];
            $this->defaultImageRelativePath = $parameters['defaultImageRelativePath'];
            $this->qualityJpeg = $parameters['qualityJpeg'];
            $this->qualityPng = $parameters['qualityPng'];
    }

}
