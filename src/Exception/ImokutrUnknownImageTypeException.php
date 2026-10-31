<?php

declare(strict_types=1);

namespace SkachCz\Imokutr3\Exception;

/**
 * @author Vladimir Skach
 */
class ImokutrUnknownImageTypeException extends \RuntimeException
{
    public function __construct(?int $type = null, string $path = '', ?string $message = null)
    {
        if ($message === null) {
            $message = $type === null
                ? sprintf('Unknown image type. Filename: "%s".', $path)
                : sprintf('Unknown image type %s. Filename: "%s".', $type, $path);
        }

        parent::__construct($message, ExceptionCodes::UNKNOWN_IMAGE_TYPE);
    }
}
