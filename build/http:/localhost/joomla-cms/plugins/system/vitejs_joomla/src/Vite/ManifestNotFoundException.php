<?php

namespace Vite;

defined('_JEXEC') or die;

use InvalidArgumentException;

class ManifestNotFoundException extends InvalidArgumentException
{
    public static function forPath(string $path)
    {
        return new static(
            sprintf('Cannot load vite manifest from `%s`', $path),
            404
        );
    }
}
