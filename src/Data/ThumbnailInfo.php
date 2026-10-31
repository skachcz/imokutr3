<?php

declare(strict_types=1);

namespace SkachCz\Imokutr3\Data;

/**
 * @author Vladimir Skach
 */
final readonly class ThumbnailInfo
{
    public function __construct(
        public string $url,
        public int $width,
        public int $height,
    ) {
    }
}
