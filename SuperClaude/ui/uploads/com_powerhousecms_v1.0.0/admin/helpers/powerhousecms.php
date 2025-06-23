<?php
/**
 * @package     PowerhouseCMS
 * @subpackage  com_powerhousecms
 * @copyright   Copyright (C) 2024 PowerhouseCMS. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Object\CMSObject;

/**
 * PowerhouseCMS component helper.
 *
 * @since  1.0.0
 */
class PowerhouseCMSHelper extends CMSObject
{
    /**
     * Configure the Linkbar.
     *
     * @param   string  $vName  The name of the active view.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public static function addSubmenu($vName)
    {
        JHtmlSidebar::addEntry(
            Text::_('COM_POWERHOUSECMS_SUBMENU_DASHBOARD'),
            'index.php?option=com_powerhousecms&view=dashboard',
            $vName == 'dashboard'
        );

        JHtmlSidebar::addEntry(
            Text::_('COM_POWERHOUSECMS_SUBMENU_CONTENT'),
            'index.php?option=com_powerhousecms&view=content',
            $vName == 'content'
        );

        JHtmlSidebar::addEntry(
            Text::_('COM_POWERHOUSECMS_SUBMENU_MEDIA'),
            'index.php?option=com_powerhousecms&view=media',
            $vName == 'media'
        );

        JHtmlSidebar::addEntry(
            Text::_('COM_POWERHOUSECMS_SUBMENU_TEMPLATES'),
            'index.php?option=com_powerhousecms&view=templates',
            $vName == 'templates'
        );
    }

    /**
     * Gets a list of the actions that can be performed.
     *
     * @param   string   $component  The component name.
     * @param   string   $section    The access section name.
     * @param   integer  $id        The item ID.
     *
     * @return  CMSObject
     *
     * @since   1.0.0
     */
    public static function getActions($component = 'com_powerhousecms', $section = 'component', $id = 0)
    {
        $user   = Factory::getUser();
        $result = new CMSObject;

        $actions = array(
            'core.admin', 'core.manage', 'core.create', 'core.edit',
            'core.edit.state', 'core.delete', 'core.edit.own'
        );

        foreach ($actions as $action)
        {
            $result->set($action, $user->authorise($action, $component));
        }

        return $result;
    }

    /**
     * Check if API connection is working.
     *
     * @return  boolean
     *
     * @since   1.0.0
     */
    public static function checkApiConnection()
    {
        $params = JComponentHelper::getParams('com_powerhousecms');
        $apiEndpoint = $params->get('api_endpoint');

        if (empty($apiEndpoint))
        {
            return false;
        }

        try
        {
            $http = JHttpFactory::getHttp();
            $response = $http->get($apiEndpoint . '/status');

            return ($response->code == 200);
        }
        catch (Exception $e)
        {
            return false;
        }
    }
}
