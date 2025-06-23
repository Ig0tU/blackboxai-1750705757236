<?php
/**
 * @package     PowerhouseCMS
 * @subpackage  com_powerhousecms
 * @copyright   Copyright (C) 2024 PowerhouseCMS. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

namespace Joomla\Component\PowerhouseCMS\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

/**
 * PowerhouseCMS Display Controller
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
    protected $default_view = 'dashboard';

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
        $view   = $this->input->get('view', 'dashboard');
        $layout = $this->input->get('layout', 'default');
        $id     = $this->input->getInt('id');

        // Check for edit form.
        if ($view == 'content' && $layout == 'edit' && !$this->checkEditId('com_powerhousecms.edit.content', $id))
        {
            // Somehow the person just went to the form - we don't allow that.
            if (!\count($this->app->getMessageQueue()))
            {
                $this->setMessage(Text::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id), 'error');
            }

            $this->setRedirect(Route::_('index.php?option=com_powerhousecms&view=contents', false));

            return false;
        }

        return parent::display($cachable, $urlparams);
    }

    /**
     * Method to get the PowerhouseCMS API status
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function getApiStatus()
    {
        $app = Factory::getApplication();
        $params = $app->getParams();
        $apiEndpoint = $params->get('api_endpoint', 'http://localhost:8000/api');

        try {
            $response = file_get_contents($apiEndpoint . '/status');
            $data = json_decode($response, true);
            
            if ($data && isset($data['status']) && $data['status'] === 'running') {
                $app->enqueueMessage(Text::_('COM_POWERHOUSECMS_API_STATUS_ONLINE'), 'success');
            } else {
                $app->enqueueMessage(Text::_('COM_POWERHOUSECMS_API_STATUS_ERROR'), 'error');
            }
        } catch (Exception $e) {
            $app->enqueueMessage(Text::_('COM_POWERHOUSECMS_API_STATUS_OFFLINE'), 'error');
        }

        $this->setRedirect(Route::_('index.php?option=com_powerhousecms', false));
    }

    /**
     * Method to sync content from PowerhouseCMS
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function syncContent()
    {
        $app = Factory::getApplication();
        $model = $this->getModel('Dashboard');
        
        try {
            $result = $model->syncContentFromAPI();
            
            if ($result) {
                $app->enqueueMessage(Text::_('COM_POWERHOUSECMS_SYNC_SUCCESS'), 'success');
            } else {
                $app->enqueueMessage(Text::_('COM_POWERHOUSECMS_SYNC_ERROR'), 'error');
            }
        } catch (Exception $e) {
            $app->enqueueMessage(Text::sprintf('COM_POWERHOUSECMS_SYNC_EXCEPTION', $e->getMessage()), 'error');
        }

        $this->setRedirect(Route::_('index.php?option=com_powerhousecms', false));
    }
}
