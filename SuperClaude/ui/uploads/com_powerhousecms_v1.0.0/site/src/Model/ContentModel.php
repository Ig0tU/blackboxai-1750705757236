<?php
/**
 * @package     PowerhouseCMS
 * @subpackage  com_powerhousecms
 * @copyright   Copyright (C) 2024 PowerhouseCMS. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

namespace Joomla\Component\PowerhouseCMS\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;

/**
 * Content model class.
 *
 * @since  1.0.0
 */
class ContentModel extends ListModel
{
    /**
     * Constructor.
     *
     * @param   array  $config  An optional associative array of configuration settings.
     *
     * @see     \JModelLegacy
     * @since   1.0.0
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = array(
                'id', 'a.id',
                'title', 'a.title',
                'alias', 'a.alias',
                'type', 'a.type',
                'status', 'a.status',
                'author', 'a.author',
                'featured', 'a.featured',
                'ordering', 'a.ordering',
                'created', 'a.created',
                'created_by', 'a.created_by',
                'modified', 'a.modified',
                'modified_by', 'a.modified_by',
                'hits', 'a.hits',
                'language', 'a.language'
            );
        }

        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * @param   string  $ordering   An optional ordering field.
     * @param   string  $direction  An optional direction (asc|desc).
     *
     * @return  void
     *
     * @since   1.0.0
     */
    protected function populateState($ordering = 'a.created', $direction = 'desc')
    {
        $app = Factory::getApplication();

        // List state information
        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->get('list_limit'), 'uint');
        $this->setState('list.limit', $limit);

        $limitstart = $app->input->get('limitstart', 0, 'uint');
        $this->setState('list.start', $limitstart);

        $orderCol = $app->input->get('filter_order', $ordering);

        if (!in_array($orderCol, $this->filter_fields))
        {
            $orderCol = $ordering;
        }

        $this->setState('list.ordering', $orderCol);

        $listOrder = $app->input->get('filter_order_Dir', $direction);

        if (!in_array(strtolower($listOrder), array('asc', 'desc', '')))
        {
            $listOrder = $direction;
        }

        $this->setState('list.direction', $listOrder);

        $params = $app->getParams();
        $this->setState('params', $params);

        $user = Factory::getUser();

        if ((!$user->authorise('core.edit.state', 'com_powerhousecms')) && (!$user->authorise('core.edit', 'com_powerhousecms')))
        {
            // Filter on published for those who do not have edit or edit.state rights.
            $this->setState('filter.published', 1);
        }

        $this->setState('filter.language', $app->getLanguageFilter());
    }

    /**
     * Method to get a store id based on model configuration state.
     *
     * @param   string  $id  A prefix for the store id.
     *
     * @return  string  A store id.
     *
     * @since   1.0.0
     */
    protected function getStoreId($id = '')
    {
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.published');
        $id .= ':' . $this->getState('filter.category_id');
        $id .= ':' . $this->getState('filter.author_id');
        $id .= ':' . $this->getState('filter.language');

        return parent::getStoreId($id);
    }

    /**
     * Get the master query for retrieving a list of content items.
     *
     * @return  \JDatabaseQuery
     *
     * @since   1.0.0
     */
    protected function getListQuery()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        // Select the required fields from the table
        $query->select(
            $this->getState(
                'list.select',
                [
                    $db->quoteName('a.id'),
                    $db->quoteName('a.title'),
                    $db->quoteName('a.alias'),
                    $db->quoteName('a.content'),
                    $db->quoteName('a.type'),
                    $db->quoteName('a.status'),
                    $db->quoteName('a.author'),
                    $db->quoteName('a.featured'),
                    $db->quoteName('a.ordering'),
                    $db->quoteName('a.created'),
                    $db->quoteName('a.created_by'),
                    $db->quoteName('a.modified'),
                    $db->quoteName('a.modified_by'),
                    $db->quoteName('a.hits'),
                    $db->quoteName('a.language')
                ]
            )
        );

        $query->from($db->quoteName('#__powerhousecms_content', 'a'));

        // Join over the users for the author
        $query->select(
            [
                $db->quoteName('ua.name', 'author_name'),
                $db->quoteName('um.name', 'modifier_name')
            ]
        )
            ->join(
                'LEFT',
                $db->quoteName('#__users', 'ua'),
                $db->quoteName('ua.id') . ' = ' . $db->quoteName('a.created_by')
            )
            ->join(
                'LEFT',
                $db->quoteName('#__users', 'um'),
                $db->quoteName('um.id') . ' = ' . $db->quoteName('a.modified_by')
            );

        // Filter by published state
        $published = $this->getState('filter.published');

        if (is_numeric($published))
        {
            $query->where($db->quoteName('a.status') . ' = ' . (int) $published);
        }
        elseif ($published === '')
        {
            $query->where('(' . $db->quoteName('a.status') . ' = 0 OR ' . $db->quoteName('a.status') . ' = 1)');
        }

        // Filter by search
        $search = $this->getState('filter.search');

        if (!empty($search))
        {
            if (stripos($search, 'id:') === 0)
            {
                $query->where($db->quoteName('a.id') . ' = ' . (int) substr($search, 3));
            }
            else
            {
                $search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
                $query->where(
                    '(' . $db->quoteName('a.title') . ' LIKE ' . $search
                    . ' OR ' . $db->quoteName('a.alias') . ' LIKE ' . $search . ')'
                );
            }
        }

        // Filter by language
        if ($this->getState('filter.language'))
        {
            $query->where($db->quoteName('a.language') . ' = ' . $db->quote($this->getState('filter.language')));
        }

        // Add the list ordering clause
        $orderCol = $this->state->get('list.ordering', 'a.created');
        $orderDirn = $this->state->get('list.direction', 'DESC');

        if ($orderCol == 'a.ordering')
        {
            $orderCol = 'a.created';
        }

        $query->order($db->escape($orderCol . ' ' . $orderDirn));

        return $query;
    }
}
