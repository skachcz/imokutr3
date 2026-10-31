<?php

declare(strict_types=1);

namespace SkachCz\Imokutr3\Data;

/**
 * @author Vladimir Skach
 */
final readonly class ImageInfo
{
    public function __construct(
        public int $width,
        public int $height,
    ) {
    }
}
