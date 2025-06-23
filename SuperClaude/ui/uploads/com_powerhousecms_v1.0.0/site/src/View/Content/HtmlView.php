<?php
/**
 * @package     PowerhouseCMS
 * @subpackage  com_powerhousecms
 * @copyright   Copyright (C) 2024 PowerhouseCMS. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

namespace Joomla\Component\PowerhouseCMS\Site\View\Content;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

/**
 * Content view class
 *
 * @since  1.0.0
 */
class HtmlView extends BaseHtmlView
{
    /**
     * The items to display
     *
     * @var  array
     */
    protected $items;

    /**
     * The pagination object
     *
     * @var  \Joomla\CMS\Pagination\Pagination
     */
    protected $pagination;

    /**
     * The model state
     *
     * @var  \Joomla\CMS\Object\CMSObject
     */
    protected $state;

    /**
     * The params object
     *
     * @var  \Joomla\Registry\Registry
     */
    protected $params;

    /**
     * Execute and display a template script.
     *
     * @param   string  $tpl  The name of the template file to parse
     *
     * @return  mixed  A string if successful, otherwise an Error object.
     */
    public function display($tpl = null)
    {
        $app = Factory::getApplication();
        $user = Factory::getUser();

        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');
        $this->params = $app->getParams();

        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            throw new \Exception(implode("\n", $errors), 500);
        }

        // Prepare the content
        foreach ($this->items as &$item)
        {
            $item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;
            $item->params = clone $this->params;
            
            // Check the view access
            if (!$user->authorise('core.view', 'com_powerhousecms.content.' . $item->id))
            {
                $item->params->set('access-view', false);
            }
        }

        // Set up page display options
        $this->prepareDocument();

        return parent::display($tpl);
    }

    /**
     * Prepares the document
     *
     * @return  void
     */
    protected function prepareDocument()
    {
        $app = Factory::getApplication();
        $menus = $app->getMenu();
        $title = null;

        // Because the application sets a default page title,
        // we need to get it from the menu item itself
        $menu = $menus->getActive();

        if ($menu)
        {
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        }
        else
        {
            $this->params->def('page_heading', Text::_('COM_POWERHOUSECMS_DEFAULT_PAGE_TITLE'));
        }

        $title = $this->params->get('page_title', '');

        if (empty($title))
        {
            $title = $app->get('sitename');
        }
        elseif ($app->get('sitename_pagetitles', 0) == 1)
        {
            $title = Text::sprintf('JPAGETITLE', $app->get('sitename'), $title);
        }
        elseif ($app->get('sitename_pagetitles', 0) == 2)
        {
            $title = Text::sprintf('JPAGETITLE', $title, $app->get('sitename'));
        }

        $this->document->setTitle($title);

        if ($this->params->get('menu-meta_description'))
        {
            $this->document->setDescription($this->params->get('menu-meta_description'));
        }

        if ($this->params->get('robots'))
        {
            $this->document->setMetaData('robots', $this->params->get('robots'));
        }
    }
}
