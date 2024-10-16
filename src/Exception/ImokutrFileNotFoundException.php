<?php

namespace SkachCz\Imokutr3\Exception;

/**
 * @package SkachCz\Imokutr3\Exception
 * @author  Vladimir Skach
 */
class ImokutrFileNotFoundException extends \RuntimeException
{
    public function __construct(string $path = null, string $message = null)
    {
        if (null === $message) {
            if (null === $path) {
                $message = 'Image file could not be found.';
            } else {
                $message = sprintf('Image file "%s" could not be found.', $path);
            }
        } else {
            $message = sprintf($message, $path);
        }

        parent::__construct($message, ExceptionCodes::FILE_NOT_FOUND);
    }
}
