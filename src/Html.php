<?php
namespace SkachCz\Imokutr3;

use SkachCz\Imokutr3\Config;
use SkachCz\Imokutr3\Data\ThumbnailInfo;
use SkachCz\Imokutr3\Image;
use SkachCz\Imokutr3\Thumbnail;

use SkachCz\Imokutr3\Exception\ImokutrWrongMacroParameterException;

/**
 * Main class
 *
 * @package SkachCz\Imokutr
 * @author  Vladimir Skach
 */
class Html
{
    /**
     * @param null|array<string,string> $attributes
     */
    public static function img(ThumbnailInfo $img, string $alt = "", string $title = "", ?array $attributes = null): string
    {

        $attText = "";

        if (($attributes !== null) && is_array($attributes)) {
            foreach ($attributes as $att => $val) {
                $attText .= sprintf('%s = "%s" ', $att, str_replace('"', '&quot;', $val));
            }
        }

        $tag = sprintf(
            '<img src="%s" width="%d" height="%d" alt="%s" title="%s" %s>',
            $img->url,
            $img->width,
            $img->height,
            $alt,
            $title,
            $attText
        );

        return $tag;
    }
}
