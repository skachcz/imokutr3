<?php
namespace SkachCz\Imokutr3\DI\Nette;

use SkachCz\Imokutr3\Imokutr;
use SkachCz\Imokutr3\ImokutrConfig;
use SkachCz\Imokutr3\Exception\ImokutrNetteMissingExtension;

use SkachCz\Imokutr3\DI\Nette\ImokutrFilters;
use SkachCz\Imokutr3\DI\Nette\ImokutrMacros;

use Nette\Schema\Schema;
use Nette\Schema\Expect;

use Nette\DI\CompilerExtension;
use Nette\Utils\ArrayHash;
use Tracy\Debugger;

// if (!class_exists('Nette\DI\CompilerExtension')) {
    //throw new ImokutrNetteMissingExtension();
// }

/**
 * Imokutr Nette extension (for Nette 2.4)
 *
 * @package SkachCz\Imokutr\Nette
 * @author  Vladimir Skach
 */
class ExtensionTools
{
    /**
     * @param array<string,mixed>|object $parameters
     */
    public static function createConfigFromArray($parameters): ImokutrConfig
    {
        if (is_array($parameters)) {
            $parameters = ArrayHash::from($parameters);
        }

        $imokutrConfig = new ImokutrConfig();

        $imokutrConfig->setConfig(
            $parameters->originalRootPath ?? '',
            $parameters->thumbsRootPath ?? '',
            $parameters->thumbsRootRelativePath ?? '',
            $parameters->defaultImageRelativePath ?? '',
            $parameters->qualityJpeg ?? null,
            $parameters->qualityPng ?? null
        );

            Debugger::barDump($imokutrConfig, 'imk config');

            return $imokutrConfig;
    }
}
