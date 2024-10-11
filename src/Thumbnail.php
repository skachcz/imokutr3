<?php
namespace SkachCz\Imokutr3;

use GdImage;
use SkachCz\Imokutr3\Config;
use SkachCz\Imokutr3\Data\ThumbnailInfo;
use SkachCz\Imokutr3\Exception\ImokutrFileNotFoundException;
use SkachCz\Imokutr3\Image;
use SkachCz\Imokutr3\ImageTools;
use SkachCz\Imokutr3\Exception\ImokutrUnknownImageTypeException;

use Tracy\Debugger;

/**
 * Thumbnail class
 *
 * @package SkachCz\Imokutr
 * @author Vladimir Skach
 */
class Thumbnail {

    public Config $config;

    public Image $image;

    public bool $isAvailable;

    public int $width;

    public int $height;

    public int $targetWidth;

    public int $targetHeight;

    public int $fixedDimension;

    public int $cropType;

    public function __construct(Config $config, Image $image)
	{
        $this->config = $config;
        $this->image = $image;
        $this->isAvailable = false;
    }

    public function getThumbnailData(): ?ThumbnailInfo {

        if ($this->isAvailable) {
            return(
                new ThumbnailInfo($this->getThumbnailUrl(), $this->width, $this->height)
            );
        } else {
            return null;
        }
    }

    /**
    * Returns thumbnail url
    */
    public function getThumbnailUrl(): string {

        Debugger::barDump($this->config, 'config');
        Debugger::barDump($this->image, 'image');

        return $this->config->thumbsRootRelativePath
            . ( $this->image->relpath == null ? '' : '/' . trim($this->image->relpath, '/') )
            . '/' . $this->getThumbnalFilename();
    }

    public function setResize(int $width, int $height, int $fixedDimension = Image::DIM_WIDTH, int $cropType = Image::CROP_CENTER): void {

        $this->width = $width;
        $this->height = $height;
        $this->targetWidth = $width;
        $this->targetHeight = $height;
        $this->fixedDimension = $fixedDimension;
        $this->cropType = $cropType;

    }

    /**
     * Processes image and returns thumbnail data
     */
    public function processImage(bool $force = false): ?ThumbnailInfo {

        $targetPath = $this->config->thumbsRootPath . '/' . $this->image->relpath;
        $targetFile = $targetPath . '/' . $this->getThumbnalFilename();

        if ($force || (!file_exists($targetFile))) {

            if(!file_exists($targetPath) && !is_dir($targetPath)) {
                @mkdir($targetPath, 0775, TRUE);
            }

            $this->createThumbnail($targetFile , $this->targetWidth, $this->targetHeight);
            $this->isAvailable = TRUE;

        } else {
            // we will get dimensions from already existing file
            $imageInfo =  getimagesize($targetFile);
            $this->width = $imageInfo[0] ?? 0;
            $this->height = $imageInfo[1] ?? 0;

            $this->isAvailable = TRUE;
        }

        return $this->getThumbnailData();
    }

    /**
    * @return string Returns thumbnail filename
    */
    public function getThumbnalFilename() {

        return ltrim($this->image->filebase, '/') . "-" . $this->targetWidth . "x" . $this->targetHeight . "-" . $this->fixedDimension
        . "-" . $this->cropType . "." . $this->image->fileext;
    }

    /**
    * @return string
    */
    public function createThumbnail(string $targetPath, int $width, int $height) {

        return $this->resizeImage($targetPath, $width, $height, $this->image->type, $this->cropType);

    }

    /**
     * Creates thumbnail image and saves it to disk
     *
     * @return string
     */
    private function resizeImage(string $targetPath, int $width, int $height, int $type = null, int $cropType = Image::CROP_CENTER) {

        $origWidth = $this->image->width;
        $origHeight = $this->image->height;

        $src = $this->createImageFrom($this->image->fullpath, $type);

        if (empty($src)) {
            throw new ImokutrFileNotFoundException($this->image->fullpath);
        }

        // Cropping image, if needed:
        if ($this->fixedDimension == Image::DIM_CROP) {

            $cr = ImageTools::cropSize($origWidth, $origHeight, $width, $height, $cropType);

            $src2 = \imagecreatetruecolor($cr->width, $cr->height);

            \imagealphablending($src2, false);
            \imagesavealpha($src2, true);

            imagecopyresampled(
                $src2, $src,
                0, 0,
                $cr->cropX, $cr->cropY,
                $cr->width, $cr->height,
                $cr->width, $cr->height
            );

            $src = $src2;

            $origWidth = $cr->width;
            $origHeight = $cr->height;
        }

        $newImgInfo = ImageTools::resizeRatio($origWidth, $origHeight, $width, $height, $this->fixedDimension);

        $img = \imagecreatetruecolor($newImgInfo->width, $newImgInfo->height);

        switch($type) {

            case IMAGETYPE_JPEG:

                \imagecopyresampled($img, $src, 0, 0, 0, 0, $newImgInfo->width, $newImgInfo->height, $origWidth, $origHeight);
                \imagejpeg($img, $targetPath, $this->config->qualityJpeg);
            break;

            case IMAGETYPE_PNG:

                \imagealphablending($img, false );
                \imagesavealpha($img, true );
                \imagecopyresampled($img, $src, 0, 0, 0, 0, $newImgInfo->width, $newImgInfo->height, $origWidth, $origHeight);

                \imagepng($img, $targetPath, $this->config->qualityPng);
            break;

            case IMAGETYPE_GIF:

                // check transparency
                $tIndex = imagecolortransparent($src);

                if ($tIndex >= 0) {
                    $tColor  = \imagecolorsforindex($src, $tIndex);

                    $transparency = (int) \imagecolorallocate($img, $tColor['red'], $tColor['green'], $tColor['blue']);
                    \imagefill($img, 0, 0, $transparency);
                    \imagecolortransparent($img, $transparency);
                } else {
                    \imagealphablending($img, false);
                    \imagesavealpha($img, true );
                }

                \imagecopyresampled($img, $src, 0, 0, 0, 0, $newImgInfo->width, $newImgInfo->height, $origWidth, $origHeight);

                \imagegif($img, $targetPath);
            break;

            default:
                throw new ImokutrUnknownImageTypeException($type, $targetPath);
        }


        // NOTICE: copying your reference variable over to another
        // will cause imagedestroy to destroy both at once.
        // so imagedestroy($src); will destroy both $src and $src2:
        \imagedestroy($src);
        \imagedestroy($img);

        $this->width = $newImgInfo->width;
        $this->height = $newImgInfo->height;

        return $targetPath;
    }


    /**
     * Creates new image resource
     * @return resource|false
     */
    public function createImageFrom(string $path, int $imageType = null) {

        switch($imageType) {

            case IMAGETYPE_JPEG:
                return \imagecreatefromjpeg($path);

            case IMAGETYPE_PNG:
                return \imagecreatefrompng($path);

            case IMAGETYPE_GIF:
                return \imagecreatefromgif($path);

            default:
                throw new ImokutrUnknownImageTypeException($imageType, $path);
        }
    }
}
