<?php

declare(strict_types=1);

namespace SkachCz\Imokutr3;

use GdImage;
use SkachCz\Imokutr3\Data\ThumbnailInfo;
use SkachCz\Imokutr3\Exception\ImokutrUnknownImageTypeException;

/**
 * @author Vladimir Skach
 */
class Thumbnail
{
    public bool $isAvailable = false;

    public int $width = 0;

    public int $height = 0;

    public int $targetWidth = 0;

    public int $targetHeight = 0;

    public int $fixedDimension = Image::DIM_WIDTH;

    public int $cropType = Image::CROP_CENTER;

    public function __construct(
        public readonly ImokutrConfig $config,
        public readonly Image $image,
    ) {
    }

    public function getThumbnailData(): ?ThumbnailInfo
    {
        if (!$this->isAvailable) {
            return null;
        }

        return new ThumbnailInfo($this->getThumbnailUrl(), $this->width, $this->height);
    }

    /**
     * Returns thumbnail url
     */
    public function getThumbnailUrl(): string
    {
        return $this->config->thumbsRootRelativePath
            . ($this->image->relpath === null ? '' : '/' . trim($this->image->relpath, '/'))
            . '/' . $this->getThumbnalFilename();
    }

    public function setResize(
        int $width,
        int $height,
        int $fixedDimension = Image::DIM_WIDTH,
        int $cropType = Image::CROP_CENTER,
    ): void {
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
    public function processImage(bool $force = false): ThumbnailInfo
    {
        $targetPath = $this->config->thumbsRootPath . '/' . ($this->image->relpath ?? '');
        $targetFile = $targetPath . '/' . $this->getThumbnalFilename();

        if ($force || !is_file($targetFile)) {
            if (!is_dir($targetPath)) {
                @mkdir($targetPath, 0775, true);
            }

            $this->createThumbnail($targetFile, $this->targetWidth, $this->targetHeight);
        } else {
            $imageInfo = @getimagesize($targetFile);
            if ($imageInfo === false) {
                $lastError = error_get_last();
                throw new Exception\ImokutrGetImageSizeFailedException($targetFile, $lastError['message'] ?? '');
            }

            $this->width = $imageInfo[0];
            $this->height = $imageInfo[1];
        }

        $this->isAvailable = true;

        return $this->getThumbnailData()
            ?? throw new \LogicException('Thumbnail was not created.');
    }

    /**
     * @return string Returns thumbnail filename
     */
    public function getThumbnalFilename(): string
    {
        return ltrim($this->image->filebase, '/')
            . '-' . $this->targetWidth
            . 'x'
            . $this->targetHeight
            . '-'
            . $this->fixedDimension
            . '-'
            . $this->cropType
            . '.'
            . $this->image->fileext;
    }

    public function createThumbnail(string $targetPath, int $width, int $height): string
    {
        return $this->resizeImage($targetPath, $width, $height, $this->image->type, $this->cropType);
    }

    /**
     * Creates thumbnail image and saves it to disk
     */
    private function resizeImage(
        string $targetPath,
        int $width,
        int $height,
        ?int $type = null,
        int $cropType = Image::CROP_CENTER,
    ): string {
        $origWidth = $this->image->width;
        $origHeight = $this->image->height;

        $src = $this->createImageFrom($this->image->fullpath, $type);

        if ($this->fixedDimension === Image::DIM_CROP) {
            $cr = ImageTools::cropSize($origWidth, $origHeight, $width, $height, $cropType);
            $cropped = $this->createTrueColor($cr->width, $cr->height);

            \imagealphablending($cropped, false);
            \imagesavealpha($cropped, true);
            \imagecopyresampled(
                $cropped,
                $src,
                0,
                0,
                $cr->cropX,
                $cr->cropY,
                $cr->width,
                $cr->height,
                $cr->width,
                $cr->height,
            );

            \imagedestroy($src);
            $src = $cropped;
            $origWidth = $cr->width;
            $origHeight = $cr->height;
        }

        $newImgInfo = ImageTools::resizeRatio($origWidth, $origHeight, $width, $height, $this->fixedDimension);
        $img = $this->createTrueColor($newImgInfo->width, $newImgInfo->height);

        switch ($type) {
            case IMAGETYPE_JPEG:
                \imagecopyresampled(
                    $img,
                    $src,
                    0,
                    0,
                    0,
                    0,
                    $newImgInfo->width,
                    $newImgInfo->height,
                    $origWidth,
                    $origHeight,
                );
                \imagejpeg($img, $targetPath, $this->config->qualityJpeg);
                break;

            case IMAGETYPE_PNG:
                \imagealphablending($img, false);
                \imagesavealpha($img, true);
                \imagecopyresampled(
                    $img,
                    $src,
                    0,
                    0,
                    0,
                    0,
                    $newImgInfo->width,
                    $newImgInfo->height,
                    $origWidth,
                    $origHeight,
                );
                \imagepng($img, $targetPath, $this->config->qualityPng);
                break;

            case IMAGETYPE_GIF:
                $tIndex = \imagecolortransparent($src);

                if ($tIndex >= 0) {
                    $tColor = \imagecolorsforindex($src, $tIndex);
                    $transparency = \imagecolorallocate($img, $tColor['red'], $tColor['green'], $tColor['blue']);
                    if ($transparency === false) {
                        throw new \RuntimeException(sprintf('imagecolorallocate() failed for "%s".', $targetPath));
                    }

                    \imagefill($img, 0, 0, $transparency);
                    \imagecolortransparent($img, $transparency);
                } else {
                    \imagealphablending($img, false);
                    \imagesavealpha($img, true);
                }

                \imagecopyresampled(
                    $img,
                    $src,
                    0,
                    0,
                    0,
                    0,
                    $newImgInfo->width,
                    $newImgInfo->height,
                    $origWidth,
                    $origHeight,
                );
                \imagegif($img, $targetPath);
                break;

            default:
                throw new ImokutrUnknownImageTypeException($type, $targetPath);
        }

        \imagedestroy($src);
        \imagedestroy($img);

        $this->width = $newImgInfo->width;
        $this->height = $newImgInfo->height;

        return $targetPath;
    }

    public function createImageFrom(string $path, ?int $imageType = null): GdImage
    {
        $image = match ($imageType) {
            IMAGETYPE_JPEG => @\imagecreatefromjpeg($path),
            IMAGETYPE_PNG => @\imagecreatefrompng($path),
            IMAGETYPE_GIF => @\imagecreatefromgif($path),
            default => throw new ImokutrUnknownImageTypeException($imageType, $path),
        };

        if ($image === false) {
            throw new Exception\ImokutrFileNotFoundException($path);
        }

        return $image;
    }

    private function createTrueColor(int $width, int $height): GdImage
    {
        if ($width < 1 || $height < 1) {
            throw new \RuntimeException(sprintf('Cannot create an image of size %dx%d.', $width, $height));
        }

        $image = \imagecreatetruecolor($width, $height);
        if ($image === false) {
            throw new \RuntimeException(sprintf('imagecreatetruecolor(%d, %d) failed.', $width, $height));
        }

        return $image;
    }
}
