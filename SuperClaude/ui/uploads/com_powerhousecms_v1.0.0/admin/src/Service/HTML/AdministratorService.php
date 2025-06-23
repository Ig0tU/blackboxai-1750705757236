<?php
/**
 * @package     PowerhouseCMS
 * @subpackage  com_powerhousecms
 * @copyright   Copyright (C) 2024 PowerhouseCMS. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

namespace PowerhouseCMS\Component\PowerhouseCMS\Administrator\Service\HTML;

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

/**
 * PowerhouseCMS HTML class.
 *
 * @since  1.0.0
 */
class AdministratorService
{
    /**
     * Get the associated language HTML
     *
     * @param   integer  $contentid  The content item id
     * @param   boolean  $tooltip    Optional. If true, displays a tooltip
     *
     * @return  string  The language HTML
     */
    public function language($contentid, $tooltip = false)
    {
        if (empty($contentid)) {
            return '';
        }

        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select($db->quoteName('language'))
            ->from($db->quoteName('#__powerhousecms_content'))
            ->where($db->quoteName('id') . ' = ' . (int) $contentid);
        $db->setQuery($query);

        try {
            $language = $db->loadResult();
        } catch (\RuntimeException $e) {
            return '';
        }

        if (empty($language)) {
            return '';
        }

        $languageCode = $language;

        if ($tooltip) {
            $title = $languageCode;

            if ($languageCode === '*') {
                $title = Text::_('JALL');
            } else {
                $title = $language;
            }

            return HTMLHelper::_('tooltip', $title, '', '', $languageCode);
        }

        return $languageCode;
    }

    /**
     * Show the featured/not-featured icon.
     *
     * @param   integer  $value      The featured value
     * @param   integer  $i          The row index
     * @param   boolean  $canChange  Whether the value can be changed or not
     *
     * @return  string  The HTML code
     */
    public function featured($value, $i, $canChange = true)
    {
        if ($canChange) {
            $states = [
                0 => ['task' => 'content.featured', 'text' => '', 'active_title' => 'COM_POWERHOUSECMS_UNFEATURED', 'inactive_title' => 'COM_POWERHOUSECMS_FEATURED', 'tip' => true, 'active_class' => 'unfeatured', 'inactive_class' => 'featured'],
                1 => ['task' => 'content.unfeatured', 'text' => '', 'active_title' => 'COM_POWERHOUSECMS_FEATURED', 'inactive_title' => 'COM_POWERHOUSECMS_UNFEATURED', 'tip' => true, 'active_class' => 'featured', 'inactive_class' => 'unfeatured'],
            ];

            return HTMLHelper::_('jgrid.state', $states, $value, $i, 'content.', $canChange);
        }

        return HTMLHelper::_('jgrid.state', $states, $value, $i, 'content.', false);
    }
}
