<?php

namespace SkachCz\Imokutr3;

use SkachCz\Imokutr3\Data\CropInfo;
use SkachCz\Imokutr3\Data\ImageInfo;
use SkachCz\Imokutr3\Image;

/**
 * Image transformation arithmetics
 *
 * @package SkachCz\Imokutr
 * @author  Vladimir Skach
 */
class ImageTools
{
    public static function resizeRatio(int $width, int $height, int $newWidth, int $newHeight, int $fixedDimension): ImageInfo
    {

        switch ($fixedDimension) {
            case Image::DIM_WIDTH:
                return ImageTools::resizeImageToWidth($width, $height, $newWidth, $newHeight);

            case Image::DIM_HEIGHT:
                return ImageTools::resizeImageToHeight($width, $height, $newWidth, $newHeight);

            case Image::DIM_CROP:
                return ImageTools::resizeImageToCrop($width, $height, $newWidth, $newHeight);

            default:
                return new ImageInfo($newWidth, $newHeight);
        }
    }

    /**
     * Returns cropped dimensions
     */
    public static function cropSize(int $width, int $height, int $targetWidth, int $targetHeight, int $cropType = Image::CROP_CENTER): CropInfo
    {

        // original image ratio
        $oRatio = $width / $height;

        // target image ratio
        $tRatio = $targetWidth / $targetHeight;

        $cropWidth = 1;
        $cropHeight = 1;

        // the original image is landscape
        if ($oRatio > 1) {
            if ($tRatio >= $oRatio) {
                $ratio = $width / $targetWidth;
                $cropWidth = $width;
                $cropHeight = intval($targetHeight * $ratio);
            } else {
                $ratio = $height / $targetHeight;
                $cropWidth = intval($targetWidth * $ratio);
                $cropHeight = $height;
            }
        } else {
            // original image is portrait or square

            if ($tRatio >= $oRatio) {
                $ratio = $width / $targetWidth;
                $cropWidth = $width;
                $cropHeight = intval($targetHeight * $ratio);
            }

            if ($tRatio < $oRatio) {
                $ratio = $height / $targetHeight;
                $cropWidth = intval($targetWidth * $ratio);
                $cropHeight = $height;
            }
        }

        // computes top left point:
        $centerX = intval(($width - $cropWidth) /2);
        $centerY = intval(($height - $cropHeight) /2);

        switch ($cropType) {
            case Image::CROP_LEFT_TOP:
                $cx = 0;
                $cy = 0;
                break;

            case Image::CROP_CENTER_TOP:
                $cx = $centerX;
                ;
                $cy = 0;
                break;

            case Image::CROP_RIGHT_TOP:
                $cx = $width - $cropWidth;
                $cy = 0;
                break;

            case Image::CROP_RIGHT_CENTER:
                $cx = $width - $cropWidth;
                $cy = $centerY;
                break;

            case Image::CROP_RIGHT_BOTTOM:
                $cx = $width - $cropWidth;
                $cy = $height - $cropHeight;
                break;

            case Image::CROP_CENTER_BOTTOM:
                $cx = $centerX;
                $cy = $height - $cropHeight;
                break;

            case Image::CROP_LEFT_BOTTOM:
                $cx = 0;
                $cy = $height - $cropHeight;
                break;

            case Image::CROP_LEFT_CENTER:
                $cx = 0;
                $cy = $centerY;
                break;

            case Image::CROP_CENTER:
            default:
                $cx = $centerX;
                $cy = $centerY;
        }

        return new CropInfo($cx, $cy, $cropWidth, $cropHeight);
    }

    /**
     * Computes new dimensions for thumbnail based on original width
     */
    public static function resizeImageToWidth(int $width, int $height, int $newWidth, int $newHeight): ImageInfo
    {

        $wRatio = $newWidth / $width;
        $newHeight = intval($height * $wRatio);

        return new ImageInfo($newWidth, $newHeight);
    }

    /**
     * Computes new dimensions for thumbnail based on original height
     */
    public static function resizeImageToHeight(int $width, int $height, int $newWidth, int $newHeight): ImageInfo
    {

        $hRatio = $newHeight / $height;
        $newWidth = intval($width * $hRatio);

        return new ImageInfo($newWidth, $newHeight);
    }

    /**
     * Computes new dimensions for thumbnail based on original ratio
     */
    public static function resizeImageToCrop(int $width, int $height, int $newWidth, int $newHeight): ImageInfo
    {

        if ($width > $height) {
            $info = ImageTools::resizeImageToHeight($width, $height, $newWidth, $newHeight);
        } else {
            $info = ImageTools::resizeImageToWidth($width, $height, $newWidth, $newHeight);
        }

        return $info;
    }
}
