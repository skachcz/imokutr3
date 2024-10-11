<?php

namespace SkachCz\Imokutr3\Exception;

/**
 * @package SkachCz\Imokutr3\Exception
 * @author  Vladimir Skach
 */
class ImokutrUnknownImageTypeException extends \RuntimeException
{

    public function __construct(int $type = null, string $path = '', string $message = null)
    {
        if (null === $message) {
            if (null === $type) {
                $message = sprintf('Unknown image type. Filename: "%s".', $path);
            } else {
                $message = sprintf('Unknown image type %s. Filename: "%s".', $type, $path);
            }
        }

        parent::__construct($message, ExceptionCodes::UNKNOWN_IMAGE_TYPE);
    }
}
