<?php

namespace SkachCz\Imokutr\Exception;

use SkachCz\Imokutr\Exception\ExceptionCodes;

/**
 * @package SkachCz\Imokutr\Exception
 * @author Vladimir Skach
 */
class ImokutrNetteMissingExtension extends \RuntimeException
{
    public function __construct(string $message = null)
    {
        $message = 'This extension needs Nette Framework installed.';
        parent::__construct($message, ExceptionCodes::MISSING_EXTENSION);
    }
}
