<?php

namespace SkachCz\Imokutr\Exception;

/**
 * @package SkachCz\Imokutr\Exception
 * @author Vladimir Skach
 */
class ImokutrNetteMissingExtension extends \RuntimeException
{

    public function __construct(string $message = null)
    {

        $code = 5;
        $message = 'The extension needs Nette Framework installed.';

        parent::__construct($message, $code);

    }

}
