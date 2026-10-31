<?php

declare(strict_types=1);

namespace SkachCz\Imokutr3\Exception;

/**
 * @author Vladimir Skach
 */
final class ExceptionCodes
{
    public const int FILE_NOT_FOUND = 1;
    public const int GET_IMAGE_SIZE_FAILED = 2;
    public const int MISSING_EXTENSION = 3;
    public const int UNKNOWN_IMAGE_TYPE = 4;
    public const int WRONG_MACRO_PARAMETER = 5;
}
