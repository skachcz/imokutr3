<?php
namespace SkachCz\Imokutr3;

/**
 * @package SkachCz\Imokutr3
 * @author Vladimir Skach
 */
class Config {

    public string $originalRootPath;
    public string $thumbsRootPath;
    public string $thumbsRootRelativePath;
    public ?string $defaultImageRelativePath;
    public int $qualityJpeg;
    public int $qualityPng;

    public function __construct(string $originalRootPath, string $thumbsRootPath, string $thumbsRootRelativePath,
                    string $defaultImageRelativePath = null, int $qualityJpeg = 75, int $qualityPng = 6) {

        $this->originalRootPath = $originalRootPath;
        $this->thumbsRootPath = $thumbsRootPath;
        $this->thumbsRootRelativePath = $thumbsRootRelativePath;
        $this->defaultImageRelativePath = $defaultImageRelativePath;
        $this->qualityJpeg = $qualityJpeg;
        $this->qualityPng = $qualityPng;

    }

    /**
    * @return array
    */
    public function getConfigArray(): array {
        return [
            'originalRootPath' => $this->originalRootPath,
            'thumbsRootPath'  => $this->thumbsRootPath,
            'thumbsRootRelativePath' => $this->thumbsRootRelativePath,
            'defaultImageRelativePath' => $this->defaultImageRelativePath,
            'qualityJpeg' => $this->qualityJpeg,
            'qualityPng' => $this->qualityPng,
        ];
    }
}