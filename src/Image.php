<?php

declare(strict_types=1);

namespace SkachCz\Imokutr3;

use SkachCz\Imokutr3\Exception\ImokutrFileNotFoundException;
use SkachCz\Imokutr3\Exception\ImokutrGetImageSizeFailedException;

/**
 * @author Vladimir Skach
 */
class Image
{
    public const int DIM_WIDTH = 1;
    public const int DIM_HEIGHT = 2;
    public const int DIM_CROP = 3;

    /*
     * Crop type constants:
     *
     *   ↖    ↑   ↗
     *     1  2  3
     *  ←  8  0  4  →
     *     7  6  5
     *   ↙    ↓   ↘
     */
    public const int CROP_CENTER = 0;
    public const int CROP_LEFT_TOP = 1;
    public const int CROP_CENTER_TOP = 2;
    public const int CROP_RIGHT_TOP = 3;
    public const int CROP_RIGHT_CENTER = 4;
    public const int CROP_RIGHT_BOTTOM = 5;
    public const int CROP_CENTER_BOTTOM = 6;
    public const int CROP_LEFT_BOTTOM = 7;
    public const int CROP_LEFT_CENTER = 8;

    public readonly ?string $relpath;

    public readonly string $imagepath;

    public readonly string $fullpath;

    public readonly string $filepath;

    public readonly string $filename;

    public readonly string $filebase;

    public readonly string $fileext;

    public readonly int $width;

    public readonly int $height;

    public readonly int $type;

    public function __construct(string $rootPath, string $imagePath, ?string $defaultImagePath = null)
    {
        $fullpath = rtrim($rootPath, '/') . '/' . ltrim($imagePath, '/');

        if ($imagePath === '' || !is_file($fullpath)) {
            if ($defaultImagePath === null || $defaultImagePath === '') {
                throw new ImokutrFileNotFoundException($fullpath);
            }

            $fullpath = rtrim($rootPath, '/') . '/' . ltrim($defaultImagePath, '/');

            if (!is_file($fullpath)) {
                throw new ImokutrFileNotFoundException($fullpath, "Default image file %s doesn't exist");
            }

            $imagePath = $defaultImagePath;
        }

        $this->imagepath = $imagePath;
        $this->fullpath = $fullpath;

        $info = $this->readImageInfo();
        $this->width = $info['width'];
        $this->height = $info['height'];
        $this->type = $info['type'];
        $this->filepath = $info['filepath'];
        $this->filebase = $info['filebase'];
        $this->fileext = $info['fileext'];
        $this->filename = $info['filename'];
        $this->relpath = $info['relpath'];
    }

    public static function dimensionFromParameter(string $fixedPar): int
    {
        return match ($fixedPar) {
            'c' => self::DIM_CROP,
            'h' => self::DIM_HEIGHT,
            default => self::DIM_WIDTH,
        };
    }

    /**
     * @return array{width: int, height: int, type: int, filepath: string, filebase: string, fileext: string, filename: string, relpath: ?string}
     */
    private function readImageInfo(): array
    {
        $imageInfo = @getimagesize($this->fullpath);

        if ($imageInfo === false) {
            $lastError = error_get_last();
            throw new ImokutrGetImageSizeFailedException($this->fullpath, $lastError['message'] ?? '');
        }

        $parts = pathinfo($this->fullpath);
        $relative = pathinfo($this->imagepath);
        $dirname = $relative['dirname'];

        return [
            'width' => $imageInfo[0],
            'height' => $imageInfo[1],
            'type' => $imageInfo[2],
            'filepath' => $parts['dirname'],
            'filebase' => $parts['filename'],
            'fileext' => $parts['extension'] ?? '',
            'filename' => $parts['basename'],
            'relpath' => $dirname === '.' ? null : $dirname,
        ];
    }
}
