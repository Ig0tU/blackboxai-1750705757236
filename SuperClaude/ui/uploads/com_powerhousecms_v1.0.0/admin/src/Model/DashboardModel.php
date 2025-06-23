<?php
/**
 * @package     PowerhouseCMS
 * @subpackage  com_powerhousecms
 * @copyright   Copyright (C) 2024 PowerhouseCMS. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

namespace Joomla\Component\PowerhouseCMS\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Http\HttpFactory;

/**
 * PowerhouseCMS Dashboard Model
 *
 * @since  1.0.0
 */
class DashboardModel extends BaseDatabaseModel
{
    /**
     * Get PowerhouseCMS API endpoint
     *
     * @return  string
     *
     * @since   1.0.0
     */
    protected function getApiEndpoint()
    {
        $params = ComponentHelper::getParams('com_powerhousecms');
        return $params->get('api_endpoint', 'http://localhost:8000/api');
    }

    /**
     * Get dashboard statistics
     *
     * @return  array
     *
     * @since   1.0.0
     */
    public function getDashboardStats()
    {
        $db = $this->getDbo();
        $stats = [];

        // Get content count
        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__powerhousecms_content'));
        $db->setQuery($query);
        $stats['content_count'] = (int) $db->loadResult();

        // Get media count
        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__powerhousecms_media'));
        $db->setQuery($query);
        $stats['media_count'] = (int) $db->loadResult();

        // Get template count
        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__powerhousecms_templates'));
        $db->setQuery($query);
        $stats['template_count'] = (int) $db->loadResult();

        // Get recent activity
        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__powerhousecms_content'))
            ->order($db->quoteName('modified') . ' DESC')
            ->setLimit(5);
        $db->setQuery($query);
        $stats['recent_content'] = $db->loadObjectList();

        return $stats;
    }

    /**
     * Sync content from PowerhouseCMS API
     *
     * @return  boolean
     *
     * @since   1.0.0
     */
    public function syncContentFromAPI()
    {
        try {
            $apiEndpoint = $this->getApiEndpoint();
            $http = HttpFactory::getHttp();
            
            // Get content from API
            $response = $http->get($apiEndpoint . '/content');
            
            if ($response->code !== 200) {
                return false;
            }

            $contentData = json_decode($response->body, true);
            
            if (!$contentData || !isset($contentData['data'])) {
                return false;
            }

            $db = $this->getDbo();
            
            foreach ($contentData['data'] as $item) {
                $this->syncContentItem($item);
            }

            return true;
        } catch (Exception $e) {
            Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
            return false;
        }
    }

    /**
     * Sync individual content item
     *
     * @param   array  $item  Content item data
     *
     * @return  boolean
     *
     * @since   1.0.0
     */
    protected function syncContentItem($item)
    {
        $db = $this->getDbo();
        
        // Check if content already exists
        $query = $db->getQuery(true)
            ->select('id')
            ->from($db->quoteName('#__powerhousecms_content'))
            ->where($db->quoteName('external_id') . ' = ' . $db->quote($item['id']));
        $db->setQuery($query);
        $existingId = $db->loadResult();

        $data = [
            'external_id' => $item['id'],
            'title' => $item['title'],
            'content' => $item['content'] ?? '',
            'type' => $item['type'] ?? 'article',
            'status' => $item['status'] ?? 'draft',
            'author' => $item['author'] ?? '',
            'created' => $item['created'] ?? Factory::getDate()->toSql(),
            'modified' => Factory::getDate()->toSql()
        ];

        if ($existingId) {
            // Update existing content
            $data['id'] = $existingId;
            $query = $db->getQuery(true)
                ->update($db->quoteName('#__powerhousecms_content'));
            
            foreach ($data as $key => $value) {
                if ($key !== 'id') {
                    $query->set($db->quoteName($key) . ' = ' . $db->quote($value));
                }
            }
            
            $query->where($db->quoteName('id') . ' = ' . (int) $existingId);
        } else {
            // Insert new content
            $query = $db->getQuery(true)
                ->insert($db->quoteName('#__powerhousecms_content'))
                ->columns($db->quoteName(array_keys($data)))
                ->values(implode(',', $db->quote(array_values($data))));
        }

        $db->setQuery($query);
        return $db->execute();
    }

    /**
     * Get API status
     *
     * @return  array
     *
     * @since   1.0.0
     */
    public function getApiStatus()
    {
        try {
            $apiEndpoint = $this->getApiEndpoint();
            $http = HttpFactory::getHttp();
            
            $response = $http->get($apiEndpoint . '/status');
            
            if ($response->code === 200) {
                $data = json_decode($response->body, true);
                return [
                    'status' => 'online',
                    'data' => $data
                ];
            }
            
            return [
                'status' => 'error',
                'message' => 'API returned status code: ' . $response->code
            ];
        } catch (Exception $e) {
            return [
                'status' => 'offline',
                'message' => $e->getMessage()
            ];
        }
    }
}
