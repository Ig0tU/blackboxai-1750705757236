<?php
/**
 * @package     PowerhouseCMS
 * @subpackage  com_powerhousecms
 * @copyright   Copyright (C) 2024 PowerhouseCMS. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;

/**
 * Script file for PowerhouseCMS Component
 *
 * @since  1.0.0
 */
class Com_PowerhouseCMSInstallerScript
{
    /**
     * Minimum PHP version required
     *
     * @var    string
     * @since  1.0.0
     */
    protected $minimumPhp = '8.1';

    /**
     * Minimum Joomla version required
     *
     * @var    string
     * @since  1.0.0
     */
    protected $minimumJoomla = '4.0';

    /**
     * Method to install the component
     *
     * @param   InstallerAdapter  $parent  The class calling this method
     *
     * @return  boolean  True on success
     *
     * @since   1.0.0
     */
    public function install($parent)
    {
        $this->setupLogging();
        Log::add('Installing PowerhouseCMS component...', Log::INFO, 'com_powerhousecms');

        // Create default categories
        $this->createDefaultCategories();

        // Initialize component settings
        $this->initializeSettings();

        Factory::getApplication()->enqueueMessage(
            Text::_('COM_POWERHOUSECMS_INSTALL_SUCCESS'),
            'success'
        );

        return true;
    }

    /**
     * Method to uninstall the component
     *
     * @param   InstallerAdapter  $parent  The class calling this method
     *
     * @return  boolean  True on success
     *
     * @since   1.0.0
     */
    public function uninstall($parent)
    {
        $this->setupLogging();
        Log::add('Uninstalling PowerhouseCMS component...', Log::INFO, 'com_powerhousecms');

        Factory::getApplication()->enqueueMessage(
            Text::_('COM_POWERHOUSECMS_UNINSTALL_SUCCESS'),
            'success'
        );

        return true;
    }

    /**
     * Method to update the component
     *
     * @param   InstallerAdapter  $parent  The class calling this method
     *
     * @return  boolean  True on success
     *
     * @since   1.0.0
     */
    public function update($parent)
    {
        $this->setupLogging();
        Log::add('Updating PowerhouseCMS component...', Log::INFO, 'com_powerhousecms');

        Factory::getApplication()->enqueueMessage(
            Text::sprintf('COM_POWERHOUSECMS_UPDATE_SUCCESS', $parent->get('manifest')->version),
            'success'
        );

        return true;
    }

    /**
     * Method to run before an install/update/uninstall method
     *
     * @param   string            $type    The type of change (install, update or discover_install)
     * @param   InstallerAdapter  $parent  The class calling this method
     *
     * @return  boolean  True on success
     *
     * @since   1.0.0
     */
    public function preflight($type, $parent)
    {
        // Check minimum PHP version
        if (!version_compare(PHP_VERSION, $this->minimumPhp, 'ge')) {
            Factory::getApplication()->enqueueMessage(
                Text::sprintf('COM_POWERHOUSECMS_MINIMUM_PHP_VERSION', $this->minimumPhp),
                'error'
            );
            return false;
        }

        // Check minimum Joomla version
        if (!version_compare(JVERSION, $this->minimumJoomla, 'ge')) {
            Factory::getApplication()->enqueueMessage(
                Text::sprintf('COM_POWERHOUSECMS_MINIMUM_JOOMLA_VERSION', $this->minimumJoomla),
                'error'
            );
            return false;
        }

        return true;
    }

    /**
     * Method to run after an install/update/uninstall method
     *
     * @param   string            $type    The type of change (install, update or discover_install)
     * @param   InstallerAdapter  $parent  The class calling this method
     *
     * @return  boolean  True on success
     *
     * @since   1.0.0
     */
    public function postflight($type, $parent)
    {
        // Additional post-installation tasks can be added here
        return true;
    }

    /**
     * Setup logging for the component
     *
     * @return  void
     *
     * @since   1.0.0
     */
    protected function setupLogging()
    {
        Log::addLogger(
            ['text_file' => 'com_powerhousecms.log.php'],
            Log::ALL,
            ['com_powerhousecms']
        );
    }

    /**
     * Create default categories
     *
     * @return  void
     *
     * @since   1.0.0
     */
    protected function createDefaultCategories()
    {
        $db = Factory::getDbo();
        $categories = [
            'Blog Posts' => 'Default category for blog posts',
            'Pages' => 'Default category for pages',
            'News' => 'Default category for news articles',
            'Documentation' => 'Default category for documentation'
        ];

        foreach ($categories as $title => $description) {
            $query = $db->getQuery(true)
                ->select('id')
                ->from('#__powerhousecms_categories')
                ->where('title = ' . $db->quote($title));
            $db->setQuery($query);
            
            if (!$db->loadResult()) {
                $category = (object) [
                    'title' => $title,
                    'alias' => strtolower(str_replace(' ', '-', $title)),
                    'description' => $description,
                    'published' => 1,
                    'access' => 1,
                    'params' => '{}',
                    'metadata' => '{}',
                    'language' => '*',
                    'created_time' => Factory::getDate()->toSql(),
                    'created_user_id' => Factory::getUser()->id,
                    'modified_time' => Factory::getDate()->toSql(),
                    'modified_user_id' => Factory::getUser()->id
                ];

                $db->insertObject('#__powerhousecms_categories', $category);
            }
        }
    }

    /**
     * Initialize component settings
     *
     * @return  void
     *
     * @since   1.0.0
     */
    protected function initializeSettings()
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select('extension_id')
            ->from('#__extensions')
            ->where('type = ' . $db->quote('component'))
            ->where('element = ' . $db->quote('com_powerhousecms'));
        
        $db->setQuery($query);
        $extensionId = $db->loadResult();

        if ($extensionId) {
            $params = [
                'api_endpoint' => 'http://localhost:8000/api',
                'enable_cache' => 1,
                'cache_time' => 3600,
                'sync_interval' => 300,
                'enable_logging' => 1,
                'debug_mode' => 0
            ];

            $query = $db->getQuery(true)
                ->update('#__extensions')
                ->set('params = ' . $db->quote(json_encode($params)))
                ->where('extension_id = ' . (int) $extensionId);
            
            $db->setQuery($query);
            $db->execute();
        }
    }
}
