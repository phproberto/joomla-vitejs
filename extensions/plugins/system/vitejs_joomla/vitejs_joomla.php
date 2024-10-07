<?php

defined('_JEXEC') or die;

require_once __DIR__ . '/vendor/autoload.php';

use Joomla\CMS\Factory;
use Phproberto\Vite\Vite;
use Phproberto\Vite\ViteEntry;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Application\CMSApplication;
use Phproberto\Vite\ViteEntryConfiguration;

class PlgSystemVitejs_Joomla extends CMSPlugin
{
    /**
     * Application object
     *
     * @var CMSApplication
     */
    protected $app;

    /**
     * Language files will be loaded automatically.
     *
     * @var    boolean
     */
    protected $autoloadLanguage = true;

    private function getDefaultConfig(): array
    {
        return [
            'mode' => $this->params->get('mode', ViteEntryConfiguration::MODE_DEVELOPMENT),
        ];
    }

    private function isAjaxRequest(): bool
    {
        return strtolower($this->app->input->server->get('HTTP_X_REQUESTED_WITH', '')) == 'xmlhttprequest';
    }

    private function isEnabledView(): bool
    {
        return !$this->isAjaxRequest() && $this->isHtmlDocument();
    }

    private function isHtmlDocument(): bool
    {
        return Factory::getDocument()->getType() === 'html';
    }

    /**
     * Executed after the application is initialised.
     *
     * @return  void
     */
    public function onAfterRender()
    {
        if ($this->isEnabledView()) {
            $body = $this->app->getBody();

            $vite = new Vite(new ViteEntryConfiguration($this->getDefaultConfig()));

            $this->app->setBody($vite->replaceTagsInText($body));
        }
    }
}
