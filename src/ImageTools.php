<?php

declare(strict_types=1);

namespace SkachCz\Imokutr3;

use SkachCz\Imokutr3\Data\CropInfo;
use SkachCz\Imokutr3\Data\ImageInfo;

/**
 * Image transformation arithmetics
 *
 * @author Vladimir Skach
 */
class ImageTools
{
    public static function resizeRatio(
        int $width,
        int $height,
        int $newWidth,
        int $newHeight,
        int $fixedDimension,
    ): ImageInfo {
        return match ($fixedDimension) {
            Image::DIM_WIDTH => self::resizeImageToWidth($width, $height, $newWidth, $newHeight),
            Image::DIM_HEIGHT => self::resizeImageToHeight($width, $height, $newWidth, $newHeight),
            Image::DIM_CROP => self::resizeImageToCrop($width, $height, $newWidth, $newHeight),
            default => new ImageInfo($newWidth, $newHeight),
        };
    }

    /**
     * Returns cropped dimensions
     */
    public static function cropSize(
        int $width,
        int $height,
        int $targetWidth,
        int $targetHeight,
        int $cropType = Image::CROP_CENTER,
    ): CropInfo {
        $oRatio = $width / $height;
        $tRatio = $targetWidth / $targetHeight;

        $cropWidth = 1;
        $cropHeight = 1;

        if ($oRatio > 1) {
            if ($tRatio >= $oRatio) {
                $ratio = $width / $targetWidth;
                $cropWidth = $width;
                $cropHeight = (int) ($targetHeight * $ratio);
            } else {
                $ratio = $height / $targetHeight;
                $cropWidth = (int) ($targetWidth * $ratio);
                $cropHeight = $height;
            }
        } else {
            if ($tRatio >= $oRatio) {
                $ratio = $width / $targetWidth;
                $cropWidth = $width;
                $cropHeight = (int) ($targetHeight * $ratio);
            }

            if ($tRatio < $oRatio) {
                $ratio = $height / $targetHeight;
                $cropWidth = (int) ($targetWidth * $ratio);
                $cropHeight = $height;
            }
        }

        $centerX = (int) (($width - $cropWidth) / 2);
        $centerY = (int) (($height - $cropHeight) / 2);

        [$cx, $cy] = match ($cropType) {
            Image::CROP_LEFT_TOP => [0, 0],
            Image::CROP_CENTER_TOP => [$centerX, 0],
            Image::CROP_RIGHT_TOP => [$width - $cropWidth, 0],
            Image::CROP_RIGHT_CENTER => [$width - $cropWidth, $centerY],
            Image::CROP_RIGHT_BOTTOM => [$width - $cropWidth, $height - $cropHeight],
            Image::CROP_CENTER_BOTTOM => [$centerX, $height - $cropHeight],
            Image::CROP_LEFT_BOTTOM => [0, $height - $cropHeight],
            Image::CROP_LEFT_CENTER => [0, $centerY],
            default => [$centerX, $centerY],
        };

        return new CropInfo($cx, $cy, $cropWidth, $cropHeight);
    }

    /**
     * Computes new dimensions for thumbnail based on original width
     */
    public static function resizeImageToWidth(int $width, int $height, int $newWidth, int $newHeight): ImageInfo
    {
        $wRatio = $newWidth / $width;
        $newHeight = (int) ($height * $wRatio);

        return new ImageInfo($newWidth, $newHeight);
    }

    /**
     * Computes new dimensions for thumbnail based on original height
     */
    public static function resizeImageToHeight(int $width, int $height, int $newWidth, int $newHeight): ImageInfo
    {
        $hRatio = $newHeight / $height;
        $newWidth = (int) ($width * $hRatio);

        return new ImageInfo($newWidth, $newHeight);
    }

    /**
     * Computes new dimensions for thumbnail based on original ratio
     */
    public static function resizeImageToCrop(int $width, int $height, int $newWidth, int $newHeight): ImageInfo
    {
        if ($width > $height) {
            return self::resizeImageToHeight($width, $height, $newWidth, $newHeight);
        }

        return self::resizeImageToWidth($width, $height, $newWidth, $newHeight);
    }
}
