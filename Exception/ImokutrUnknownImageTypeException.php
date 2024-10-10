<?php

namespace SkachCz\Imokutr\Exception;

use SkachCz\Imokutr\Exception\ExceptionCodes;

/**
 * @package SkachCz\Imokutr\Exception
 * @author Vladimir Skach
 */
class ImokutrUnknownImageTypeException extends \RuntimeException
{
    public function __construct(int $type = null, string $path = '', string $message = null)
    {
        if (null === $message) {
            if (null === $type) {
                $message = sprintf('Unknown image type for file: "%s".', $path);
            } else {
                $message = sprintf('Unknown image type %s. for file: "%s".', $type, $path);
            }
        }

        parent::__construct($message, ExceptionCodes::UNKNOWN_IMAGE_TYPE);
    }
}
