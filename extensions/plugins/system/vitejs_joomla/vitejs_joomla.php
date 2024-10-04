<?php

defined('_JEXEC') or die;

\JLoader::registerNamespace('Phproberto\Joomla', __DIR__ .'/src', false, false, 'psr4');

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Phproberto\Joomla\Vite\ViteEntry;
use Joomla\CMS\Application\CMSApplication;

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

            $regex = '/@vite\((.+?)\)/s';
            preg_match_all($regex, $body, $matches, PREG_SET_ORDER);

            foreach ($matches as $match) {
                $entry = ViteEntry::fromRegexMatch($match);
                $output = $entry->getOutput();

                $body = str_replace($match[0], $output, $body);
            }

            $this->app->setBody($body);
        }
    }
}
