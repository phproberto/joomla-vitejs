<?php

namespace Phproberto\Joomla\Vite;

use Phproberto\Joomla\Vite\ViteEntryConfiguration;
use Phproberto\Joomla\Vite\ManifestNotFoundException;

final class ViteEntry {
    /**
     * @var array
     */
    protected $manifest;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var ViteEntryConfiguration
     */
    protected $config;

    protected static $checked = [];

    public function __construct(string $path, ViteEntryConfiguration $config)
    {
        $this->path = $path;
        $this->config = $config;
    }

    public static function fromRegexMatch($match, array $config = []): ViteEntry
    {
        $jsonString = '[' . str_replace("'", '"', $match[1]) . ']';
        $arguments = json_decode($jsonString, true);
        $path = $arguments[0];

        $config = isset($arguments[1]) ? array_merge($config, $arguments[1]) : $config;

        return new ViteEntry($path, new ViteEntryConfiguration($config));
    }

    public function getAssetUrl(string $asset): string
    {
        return $this->isDev()
                ? $this->config->getExternalViteHost() . '/' . $asset
                : $this->config->getBaseUrl()  . '/' . $asset;
    }

    public function getConfig(): ViteEntryConfiguration
    {
        return $this->config;
    }

    public function getCssTags(): string
    {
        $res = '';

        if ($this->isDev()) {
            return $res;
        }

        foreach ($this->getCssUrls() as $url) {
            $res .= sprintf('<link rel="stylesheet" href="%s">', $url);
        }

        return $res;
    }

    protected function getCssUrls(): array
    {
        $urls = [];

        if ($this->isCss()) {
            $urls[] = $this->getAssetUrl($this->path);
        }

        $manifest = $this->getManifest();

        if (!empty($manifest[$this->path]['css'])) {
            foreach ($manifest[$this->path]['css'] as $file) {
                $urls[] = $this->getAssetUrl($file);
            }
        }

        return $urls;
    }

    public function getImportsUrls(): array
    {
        $urls = [];
        $manifest = $this->getManifest();

        if (!empty($manifest[$this->path]['imports'])) {
            foreach ($manifest[$this->path]['imports'] as $imports) {
                $urls[] = $this->getAssetUrl($manifest[$imports]['file']);
            }
        }

        return $urls;
    }

    public function getJsPreloadImports(): string
    {
        $res = '';

        if ($this->isDev()) {
            return $res;
        }

        foreach ($this->getImportsUrls() as $url) {
            $res .= sprintf('<link rel="modulepreload" href="%s">', $url);
        }

        return $res;
    }

    public function getJsTags(): string
    {
        $res = '';

        foreach ($this->getJsUrls() as $url) {
            $res .= sprintf('<script type="module" crossorigin="anonymous" src="%s"></script>', $url);
        }

        return $res;
    }

    public function getJsTag(): string
    {
        if (!$this->isJs()) {
            return '';
        }

        $url = $this->getAssetUrl($this->path);

        if (empty($url)) {
            return '';
        }

        return sprintf(
            '<script type="module" crossorigin="anonymous" src="%s"></script>',
            $url
        );
    }

    protected function getjsUrls(): array
    {
        $urls = [];

        if ($this->isJs()) {
            $urls[] = $this->getAssetUrl($this->path);
        }

        $manifest = $this->getManifest();

        if (!empty($manifest[$this->path]['file']) && $this->isJsPath($manifest[$this->path]['file'])) {
            $urls[] = $this->getAssetUrl($manifest[$this->path]['file']);
        }

        if (!empty($manifest[$this->path]['js'])) {
            foreach ($manifest[$this->path]['js'] as $file) {
                $urls[] = $this->getAssetUrl($file);
            }
        }

        return $urls;
    }

    public function getManifest(): array
    {
        if (null === $this->manifest) {
            $path = $this->config->getManifestPath();

            if (!file_exists($path)) {
                throw ManifestNotFoundException::forPath($path);
            }

            $content = file_get_contents($path);

            $this->manifest = json_decode($content, true);
        }

        return $this->manifest;
    }

    public function getOutput(): string
    {
        return implode(
            "\n",
            [$this->getJsTags(), $this->getJsTag(), $this->getJsPreloadImports(), $this->getCssTags()]
        );
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function isCss()
    {
        return strtolower(pathinfo($this->path, PATHINFO_EXTENSION)) === 'css';
    }

    public function isDev(): bool
    {
        if ($this->config->isProduction()) {
            return false;
        }

        $manifest = $this->config->getManifestPath();

        if (array_key_exists($manifest, static::$checked)) {
            return static::$checked[$manifest];
        }

        $url = $this->config->getInternalViteHost() . '/' . $this->path;

        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_NOBODY, true);

        curl_exec($handle);
        $error = curl_errno($handle);
        curl_close($handle);

        static::$checked[$manifest] = !$error;

        return !$error;
    }

    protected function isJsPath($path): bool
    {
        return strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'js';
    }

    public function isJs()
    {
        return strtolower(pathinfo($this->path, PATHINFO_EXTENSION)) === 'js';
    }
}
