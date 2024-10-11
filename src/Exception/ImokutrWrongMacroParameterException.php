<?php

namespace SkachCz\Imokutr3\Exception;

/**
 * @package SkachCz\Imokutr3\Exception
 * @author  Vladimir Skach
 */
class ImokutrWrongMacroParameterException extends \RuntimeException
{

    public function __construct(string $parameter = null, string $limitText = null)
    {
        $message = sprintf('Macro parameter %s must be %s.', $parameter, $limitText);
        parent::__construct($message, ExceptionCodes::WRONG_MACRO_PARAMETER);
    }
}
