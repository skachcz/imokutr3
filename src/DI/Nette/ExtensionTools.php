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
     * @param array|object $parameters
     */
    public static function createConfigFromArray(object $parameters): ImokutrConfig
    {
            $imokutrConfig = new ImokutrConfig(
                $parameters->originalRootPath,
                $parameters->thumbsRootPath,
                $parameters->thumbsRootRelativePath,
                $parameters->defaultImageRelativePath,
                $parameters->qualityJpeg,
                $parameters->qualityPng
            );

            return $imokutrConfig;
    }
}
