<?php
/**
 * @package     PowerhouseCMS
 * @subpackage  com_powerhousecms
 * @copyright   Copyright (C) 2024 PowerhouseCMS. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

namespace Joomla\Component\PowerhouseCMS\Administrator\View\Dashboard;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;

/**
 * PowerhouseCMS Dashboard View
 *
 * @since  1.0.0
 */
class HtmlView extends BaseHtmlView
{
    /**
     * The dashboard statistics
     *
     * @var    array
     * @since  1.0.0
     */
    protected $stats;

    /**
     * The API status
     *
     * @var    array
     * @since  1.0.0
     */
    protected $apiStatus;

    /**
     * The sidebar content
     *
     * @var    string
     * @since  1.0.0
     */
    protected $sidebar;

    /**
     * Display the view
     *
     * @param   string  $tpl  The name of the template file to parse
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function display($tpl = null)
    {
        $this->stats = $this->get('DashboardStats');
        $this->apiStatus = $this->get('ApiStatus');
        
        // Check for errors
        if (count($errors = $this->get('Errors'))) {
            throw new \Exception(implode("\n", $errors), 500);
        }

        $this->addToolbar();
        $this->sidebar = \JHtmlSidebar::render();

        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    protected function addToolbar()
    {
        $canDo = ContentHelper::getActions('com_powerhousecms');

        ToolbarHelper::title(Text::_('COM_POWERHOUSECMS_DASHBOARD'), 'powerhousecms');

        if ($canDo->get('core.admin')) {
            ToolbarHelper::custom('dashboard.syncContent', 'refresh', 'refresh', 'COM_POWERHOUSECMS_SYNC_CONTENT', false);
            ToolbarHelper::custom('dashboard.getApiStatus', 'info', 'info', 'COM_POWERHOUSECMS_CHECK_API_STATUS', false);
            ToolbarHelper::divider();
            ToolbarHelper::preferences('com_powerhousecms');
        }

        // Add submenu items
        \JHtmlSidebar::addEntry(
            Text::_('COM_POWERHOUSECMS_SUBMENU_DASHBOARD'),
            'index.php?option=com_powerhousecms&view=dashboard',
            true
        );

        \JHtmlSidebar::addEntry(
            Text::_('COM_POWERHOUSECMS_SUBMENU_CONTENT'),
            'index.php?option=com_powerhousecms&view=contents'
        );

        \JHtmlSidebar::addEntry(
            Text::_('COM_POWERHOUSECMS_SUBMENU_MEDIA'),
            'index.php?option=com_powerhousecms&view=media'
        );

        \JHtmlSidebar::addEntry(
            Text::_('COM_POWERHOUSECMS_SUBMENU_TEMPLATES'),
            'index.php?option=com_powerhousecms&view=templates'
        );
    }
}
