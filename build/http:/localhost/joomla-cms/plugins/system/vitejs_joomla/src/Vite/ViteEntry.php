<?php 

namespace Vite;

use Vite\ViteEntryConfiguration;
use Vite\ManifestNotFoundException;

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

    public static function fromRegexMatch($match): ViteEntry
    {
        $jsonString = '[' . str_replace("'", '"', $match[1]) . ']';
        $arguments = json_decode($jsonString, true);
        $path = $arguments[0];

        $config = isset($arguments[1]) ? new ViteEntryConfiguration($arguments[1]) : new ViteEntryConfiguration;

        return new ViteEntry($path, $config);
    }

    public function isCss()
    {
        return strtolower(pathinfo($this->path, PATHINFO_EXTENSION)) === 'css';
    }

    public function isJs()
    {
        return strtolower(pathinfo($this->path, PATHINFO_EXTENSION)) === 'js';
    }

    public function getAssetUrl(string $asset): string
    {
        $manifest = $this->getManifest();

        return isset($manifest[$asset])
            ? '/' . $manifest[$asset]['file']
            : '';
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
            $urls[] = $this->isDev()
                ? $this->config->getExternalViteHost() . '/' . $this->path
                : $this->getAssetUrl($this->path);
        }

        $manifest = $this->getManifest();

        if (!empty($manifest[$this->path]['css'])) {
            foreach ($manifest[$this->path]['css'] as $file) {
                $urls[] = '/' . $file;
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
                $urls[] = '/' . $manifest[$imports]['file'];
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

    public function getJsTag(): string
    {
        if (!$this->isJs()) {
            return '';
        }

        $url = $this->isDev() 
            ? $this->config->getExternalViteHost() . '/' . $this->path 
            : $this->getAssetUrl($this->path);

        if (empty($url)) {
            return '';
        }

        return sprintf(
            '<script type="module" crossorigin="anonymous" src="%s"></script>',
            $url
        );
    }

    public function getManifest(): array
    {
        if (null === $this->manifest) {
            $path = JPATH_SITE . $this->config->getManifestPath();

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
            [$this->getJsTag(), $this->getJsPreloadImports(), $this->getCssTags()]
        );
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function isDev(): bool
    {
        $manifest = $this->config->getManifestPath();
        // This method is very useful for the local server
        // if we try to access it, and by any means, didn't started Vite yet
        // it will fallback to load the production files from manifest
        // so you still navigate your site as you intended!
        if (array_key_exists($manifest, static::$checked)) {
            return static::$checked[$manifest];
        } 

        $url = $this->config->getInternalViteHost() . '/' . $this->path;

        $handle = curl_init($url);
        // curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($handle, CURLOPT_NOBODY, true);

        curl_setopt($handle, CURLOPT_AUTOREFERER, TRUE);
        curl_setopt($handle, CURLOPT_HEADER, 0);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($handle, CURLOPT_URL, $url);
        curl_setopt($handle, CURLOPT_FOLLOWLOCATION, TRUE);

        curl_exec($handle);

        $error = curl_errno($handle);
        curl_close($handle);

        static::$checked[$manifest] = !$error;

        return !$error;
    }
}
