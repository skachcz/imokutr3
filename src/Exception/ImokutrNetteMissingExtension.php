<?php

declare(strict_types=1);

namespace SkachCz\Imokutr3\Exception;

/**
 * @author Vladimir Skach
 */
class ImokutrNetteMissingExtension extends \RuntimeException
{
    public function __construct(?string $message = null)
    {
        parent::__construct(
            $message ?? 'This extension needs Nette Framework installed.',
            ExceptionCodes::MISSING_EXTENSION,
        );
    }
}
