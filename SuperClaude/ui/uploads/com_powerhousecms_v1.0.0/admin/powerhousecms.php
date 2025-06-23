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
use Joomla\CMS\MVC\Controller\BaseController;

// Access check
if (!Factory::getUser()->authorise('core.manage', 'com_powerhousecms'))
{
    throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'), 404);
}

// Require the helper file
JLoader::register('PowerhouseCMSHelper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/powerhousecms.php');

// Get an instance of the controller prefixed by PowerhouseCMS
$controller = BaseController::getInstance('PowerhouseCMS');

// Perform the Request task
$controller->execute(Factory::getApplication()->input->get('task'));

// Redirect if set by the controller
$controller->redirect();
