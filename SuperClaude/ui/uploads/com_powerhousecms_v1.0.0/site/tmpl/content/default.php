<?php
/**
 * @package     PowerhouseCMS
 * @subpackage  com_powerhousecms
 * @copyright   Copyright (C) 2024 PowerhouseCMS. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.core');
HTMLHelper::_('stylesheet', 'com_powerhousecms/site.css', array('version' => 'auto', 'relative' => true));
?>

<div class="powerhousecms-content<?php echo $this->pageclass_sfx; ?>">
    <?php if ($this->params->get('show_page_heading')) : ?>
        <div class="page-header">
            <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
        </div>
    <?php endif; ?>

    <?php if (!empty($this->items)) : ?>
        <div class="powerhousecms-content-items">
            <?php foreach ($this->items as $item) : ?>
                <div class="content-item">
                    <h2>
                        <?php if ($item->params->get('access-view')) : ?>
                            <a href="<?php echo Route::_('index.php?option=com_powerhousecms&view=content&id=' . $item->id . '&catid=' . $item->catid); ?>">
                                <?php echo $this->escape($item->title); ?>
                            </a>
                        <?php else : ?>
                            <?php echo $this->escape($item->title); ?>
                        <?php endif; ?>
                    </h2>

                    <?php if ($item->state == 0) : ?>
                        <span class="badge bg-warning"><?php echo Text::_('JUNPUBLISHED'); ?></span>
                    <?php endif; ?>

                    <?php if ($item->featured) : ?>
                        <span class="badge bg-success"><?php echo Text::_('COM_POWERHOUSECMS_FEATURED'); ?></span>
                    <?php endif; ?>

                    <div class="content-meta">
                        <?php if ($item->created) : ?>
                            <span class="created-date">
                                <?php echo HTMLHelper::_('date', $item->created, Text::_('DATE_FORMAT_LC3')); ?>
                            </span>
                        <?php endif; ?>

                        <?php if ($item->author) : ?>
                            <span class="author">
                                <?php echo Text::sprintf('COM_POWERHOUSECMS_WRITTEN_BY', $this->escape($item->author)); ?>
                            </span>
                        <?php endif; ?>

                        <?php if ($item->category_title) : ?>
                            <span class="category">
                                <?php echo Text::sprintf('COM_POWERHOUSECMS_CATEGORY', $this->escape($item->category_title)); ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <?php if ($item->introtext) : ?>
                        <div class="content-intro">
                            <?php echo HTMLHelper::_('content.prepare', $item->introtext); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($item->params->get('access-view')) : ?>
                        <a href="<?php echo Route::_('index.php?option=com_powerhousecms&view=content&id=' . $item->id . '&catid=' . $item->catid); ?>" 
                           class="btn btn-primary">
                            <?php echo Text::_('COM_POWERHOUSECMS_READ_MORE'); ?>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($this->pagination->pagesTotal > 1) : ?>
            <div class="pagination">
                <?php echo $this->pagination->getPagesLinks(); ?>
            </div>
        <?php endif; ?>
    <?php else : ?>
        <div class="alert alert-info">
            <span class="icon-info-circle" aria-hidden="true"></span>
            <span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
            <?php echo Text::_('COM_POWERHOUSECMS_NO_CONTENT_FOUND'); ?>
        </div>
    <?php endif; ?>
</div>
