<?php

declare(strict_types=1);

namespace SkachCz\Imokutr3\Exception;

/**
 * @author Vladimir Skach
 */
class ImokutrWrongMacroParameterException extends \RuntimeException
{
    public function __construct(?string $parameter = null, ?string $limitText = null)
    {
        parent::__construct(
            sprintf('Macro parameter %s must be %s.', (string) $parameter, (string) $limitText),
            ExceptionCodes::WRONG_MACRO_PARAMETER,
        );
    }
}
