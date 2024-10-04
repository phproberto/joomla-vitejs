<?php

namespace Phproberto\Joomla\ViteJs\Plugin;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Application\CMSApplication;
use Wow\Joomla\Extensions\Models\Extension;

abstract class BasePlugin extends CMSPlugin
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

    /**
     * @var Extension
     */
    protected $extension;

    public function __construct(&$subject, $config = array())
    {
        parent::__construct($subject, $config);
    }

    protected function getName(): string
    {
        return Text::_('PLG_' . strtoupper($this->_type) . '_' . strtoupper($this->_name));
    }
}
