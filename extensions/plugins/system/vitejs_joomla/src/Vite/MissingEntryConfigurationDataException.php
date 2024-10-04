<?php
namespace Phproberto\Joomla\Vite;

defined('_JEXEC') or die;

use InvalidArgumentException;
use Phproberto\Joomla\Vite\ViteEntryConfiguration;

class MissingEntryConfigurationDataException extends InvalidArgumentException
{
    public static function noConfigurationGiven(string $path)
    {
        return new static(
            'No configuration was given for this vite entry: ' . $path,
            422
        );
    }

    public static function missingManifestPath(ViteEntryConfiguration $config)
    {
        return new static(
            'Missing manifest path in the vite entry. Example: @vite("main.ts", {"manifest": "/media/wow/dist/wowApp/manifest.json"})',
            422
        );
    }
}
