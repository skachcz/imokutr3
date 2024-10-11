<?php

namespace SkachCz\Imokutr3\Exception;

/**
 * @package SkachCz\Imokutr3\Exception
 * @author  Vladimir Skach
 */
class ImokutrGetImageSizeFailedException extends \RuntimeException
{

    public function __construct(string $path = '', string $errorMessage = '', string $message = null)
    {
        if (null === $message) {
                $message = sprintf('Function getimagesize() failed (%s), Filename: %s', $errorMessage, $path);
        }

        parent::__construct($message, ExceptionCodes::GET_IMAGE_SIZE_FAILED);
    }
}
