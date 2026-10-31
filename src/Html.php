<?php

declare(strict_types=1);

namespace SkachCz\Imokutr3;

use SkachCz\Imokutr3\Data\ThumbnailInfo;

/**
 * @author Vladimir Skach
 */
class Html
{
    /**
     * @param null|array<string, string> $attributes
     */
    public static function img(
        ThumbnailInfo $img,
        string $alt = '',
        string $title = '',
        ?array $attributes = null,
    ): string {
        $attText = '';

        if ($attributes !== null) {
            foreach ($attributes as $att => $val) {
                $attText .= sprintf('%s = "%s" ', $att, str_replace('"', '&quot;', $val));
            }
        }

        return sprintf(
            '<img src="%s" width="%d" height="%d" alt="%s" title="%s" %s>',
            $img->url,
            $img->width,
            $img->height,
            $alt,
            $title,
            $attText,
        );
    }
}
