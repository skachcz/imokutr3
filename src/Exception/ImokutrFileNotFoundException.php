<?php

declare(strict_types=1);

namespace SkachCz\Imokutr3\Exception;

/**
 * @author Vladimir Skach
 */
class ImokutrFileNotFoundException extends \RuntimeException
{
    public function __construct(?string $path = null, ?string $message = null)
    {
        if ($message === null) {
            $message = $path === null
                ? 'Image file could not be found.'
                : sprintf('Image file "%s" could not be found.', $path);
        } else {
            $message = sprintf($message, (string) $path);
        }

        parent::__construct($message, ExceptionCodes::FILE_NOT_FOUND);
    }
}
