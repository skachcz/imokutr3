<?php

declare(strict_types=1);

namespace SkachCz\Imokutr3\Data;

/**
 * @author Vladimir Skach
 */
final readonly class CropInfo
{
    public function __construct(
        public int $cropX,
        public int $cropY,
        public int $width,
        public int $height,
    ) {
    }
}
