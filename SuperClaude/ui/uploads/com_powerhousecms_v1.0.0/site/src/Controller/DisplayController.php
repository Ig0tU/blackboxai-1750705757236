<?php
/**
 * @package     PowerhouseCMS
 * @subpackage  com_powerhousecms
 * @copyright   Copyright (C) 2024 PowerhouseCMS. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

namespace Joomla\Component\PowerhouseCMS\Site\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;

/**
 * PowerhouseCMS Component Controller
 *
 * @since  1.0.0
 */
class DisplayController extends BaseController
{
    /**
     * The default view.
     *
     * @var    string
     * @since  1.0.0
     */
    protected $default_view = 'content';

    /**
     * Method to display a view.
     *
     * @param   boolean  $cachable   If true, the view output will be cached
     * @param   array    $urlparams  An array of safe URL parameters and their variable types
     *
     * @return  BaseController|boolean  This object to support chaining.
     *
     * @since   1.0.0
     */
    public function display($cachable = false, $urlparams = [])
    {
        $view = $this->input->get('view', $this->default_view);
        $layout = $this->input->get('layout', 'default');
        $id = $this->input->getInt('id');

        // Check for edit form.
        if ($view == 'content' && $layout == 'edit' && !$this->checkEditId('com_powerhousecms.edit.content', $id))
        {
            // Somehow the person just went to the form - we don't allow that.
            if (!\count($this->app->getMessageQueue()))
            {
                $this->setMessage(Text::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id), 'error');
            }

            $this->setRedirect(Route::_('index.php?option=com_powerhousecms&view=content', false));

            return false;
        }

        return parent::display($cachable, $urlparams);
    }
}
