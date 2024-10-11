<?php

namespace SkachCz\Imokutr3;

use SkachCz\Imokutr3\Exception\ImokutrFileNotFoundException;
use SkachCz\Imokutr3\Exception\ImokutrGetImageSizeFailedException;

use Tracy\Debugger;

/**
 * @package SkachCz\Imokutr
 * @author Vladimir Skach
 */
class Image {

    const DIM_WIDTH = 1;
    const DIM_HEIGHT = 2;
    const DIM_CROP = 3;

    /*
     * Crop type constants:
     *
     *   ↖    ↑   ↗
     *     1  2  3
     *  ←  8  0  4  →
     *     7  6  5
     *   ↙    ↓   ↘
     */
    const CROP_CENTER = 0;
    const CROP_LEFT_TOP = 1;
    const CROP_CENTER_TOP = 2;
    const CROP_RIGHT_TOP = 3;
    const CROP_RIGHT_CENTER = 4;
    const CROP_RIGHT_BOTTOM = 5;
    const CROP_CENTER_BOTTOM = 6;
    const CROP_LEFT_BOTTOM = 7;
    const CROP_LEFT_CENTER = 8;

    public ?string $relpath;

    public string $imagepath;

    public string $fullpath;

    public string $filepath;

    public string $filename;

    public string $filebase;

    public string $fileext;

    public int $width;

    public int $height;

    public int $type;

    public function __construct(string $rootPath, string $imagePath, string $defaultImagePath = null)
	{
        $fullpath = rtrim($rootPath, '/') . '/' . ltrim($imagePath, '/');

        // check if file exists
        if ( ($imagePath == null) || (!file_exists($fullpath)) ){

            if ($defaultImagePath !== null) {

                $fullpath = rtrim($rootPath, '/') . '/' . ltrim($defaultImagePath, '/');

                if (!file_exists($fullpath)) {
                    throw new ImokutrFileNotFoundException($fullpath, "Default image file %s doesn't exist");
                }

                $imagePath = $defaultImagePath;

            } else {
                throw new ImokutrFileNotFoundException($fullpath);
            }

        }

        // Debugger::barDump($this, "image");

        $this->imagepath = $imagePath;
        $this->fullpath = $fullpath;

        $this->setImageInfo();

    }

    /**
     * Sets basic image properties
     * @return void
     */
    private function setImageInfo() {

        $imageInfo = getimagesize($this->fullpath);

        $lastError = error_get_last();
        if ($lastError !== null && strpos($lastError["message"], 'getimagesize(') === 0)
        {
           throw new ImokutrGetImageSizeFailedException($this->fullpath, $lastError["message"]);
        }

        $this->width = $imageInfo[0] ?? 0;;
        $this->height = $imageInfo[1] ?? 0;
        $this->type = $imageInfo[2] ?? 0;

        $parts = pathinfo($this->fullpath);

        $this->filepath = $parts['dirname'] ?? '';
        $this->filebase = $parts['filename'];
        $this->fileext = $parts['extension'] ?? '';
        $this->filename = $parts['basename'];

        $rpath = pathinfo($this->imagepath);
        if (array_key_exists('dirname', $rpath)) {
            $this->relpath = ($rpath['dirname'] == '.' ? null : $rpath['dirname']);
        }

    }
}
