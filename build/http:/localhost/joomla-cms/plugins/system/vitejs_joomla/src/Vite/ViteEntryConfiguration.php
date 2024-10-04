<?php 

namespace Vite;

final class ViteEntryConfiguration {
    /**
     * @var array
     */
    protected $data;

    const DEFAULT_INTERNAL_HOST = 'http://localhost:5173';
    const DEFAULT_EXTERNAL_HOST = 'http://localhost:5173';

    public function __construct(array $data = []) 
    {
        $this->data = $data;
    }

    public function toArray(): array
    {
        return $data;
    }

    public function getDistFolder(): string
    {
        if (array_key_exists('distFolder', $this->data)) {
            return $this->data['distFolder'];
        }

        return dirname($this->getManifestPath());
    }

    public function getAssetsPath(): string
    {
        
    }

    /**
     * @throws MissingEntryConfigurationDataException If manifest path is not set in the configuration
     */
    public function getManifestPath(): string
    {
        if (!array_key_exists('manifest', $this->data)) {
            throw MissingEntryConfigurationDataException::missingManifestPath($this);
        }

        return $this->data['manifest'];
    }

    public function getExternalViteHost(): string
    {
        return array_key_exists('externalHost', $this->data) ? $this->data['externalHost'] : self::DEFAULT_EXTERNAL_HOST;
    }

    public function getInternalViteHost(): string
    {
        return array_key_exists('internalHost', $this->data) ? $this->data['internalHost'] :self::DEFAULT_INTERNAL_HOST;
    }
}