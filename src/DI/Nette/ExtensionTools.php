<?php

declare(strict_types=1);

namespace SkachCz\Imokutr3\DI\Nette;

use SkachCz\Imokutr3\ImokutrConfig;

/**
 * @author Vladimir Skach
 */
class ExtensionTools
{
    /**
     * @param array<string, mixed>|object $parameters
     */
    public static function createConfigFromArray(array|object $parameters): ImokutrConfig
    {
        if (is_object($parameters)) {
            $parameters = get_object_vars($parameters);
        }

        $values = [];
        foreach ($parameters as $key => $value) {
            if (!is_string($key)) {
                continue;
            }

            $values[$key] = $value;
        }

        return new ImokutrConfig(
            originalRootPath: self::stringOption($values, 'originalRootPath'),
            thumbsRootPath: self::stringOption($values, 'thumbsRootPath'),
            thumbsRootRelativePath: self::stringOption($values, 'thumbsRootRelativePath'),
            defaultImageRelativePath: self::stringOption($values, 'defaultImageRelativePath'),
            qualityJpeg: self::intOption($values, 'qualityJpeg', 75),
            qualityPng: self::intOption($values, 'qualityPng', 6),
        );
    }

    /**
     * @param array<string, mixed> $parameters
     */
    private static function stringOption(array $parameters, string $key): string
    {
        if (!array_key_exists($key, $parameters) || $parameters[$key] === null) {
            return '';
        }

        $value = $parameters[$key];
        if (!is_string($value)) {
            throw new \InvalidArgumentException(sprintf('Configuration value "%s" must be a string.', $key));
        }

        return $value;
    }

    /**
     * @param array<string, mixed> $parameters
     */
    private static function intOption(array $parameters, string $key, int $default): int
    {
        if (!array_key_exists($key, $parameters) || $parameters[$key] === null) {
            return $default;
        }

        $value = $parameters[$key];
        if (!is_int($value)) {
            throw new \InvalidArgumentException(sprintf('Configuration value "%s" must be an integer.', $key));
        }

        return $value;
    }
}
