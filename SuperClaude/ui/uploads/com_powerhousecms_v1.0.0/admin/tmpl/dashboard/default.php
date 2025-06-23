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
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.core');
HTMLHelper::_('bootstrap.framework');

$wa = $this->document->getWebAssetManager();
$wa->useStyle('com_powerhousecms.admin')
   ->useScript('com_powerhousecms.admin');
?>

<div id="j-main-container" class="j-main-container">
    <?php if (!empty($this->sidebar)) : ?>
        <div id="j-sidebar-container" class="j-sidebar-container">
            <?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="j-main-container">
    <?php endif; ?>

    <div class="powerhousecms-dashboard">
        <!-- API Status Card -->
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">
                            <?php echo Text::_('COM_POWERHOUSECMS_API_STATUS'); ?>
                        </h3>
                    </div>
                    <div class="card-body">
                        <?php if ($this->apiStatus['status'] === 'online') : ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i>
                                <?php echo Text::_('COM_POWERHOUSECMS_API_STATUS_ONLINE'); ?>
                            </div>
                        <?php elseif ($this->apiStatus['status'] === 'error') : ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <?php echo Text::_('COM_POWERHOUSECMS_API_STATUS_ERROR'); ?>
                                <br><small><?php echo htmlspecialchars($this->apiStatus['message']); ?></small>
                            </div>
                        <?php else : ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-times-circle"></i>
                                <?php echo Text::_('COM_POWERHOUSECMS_API_STATUS_OFFLINE'); ?>
                                <br><small><?php echo htmlspecialchars($this->apiStatus['message']); ?></small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h2 class="card-title text-primary"><?php echo $this->stats['content_count']; ?></h2>
                        <p class="card-text"><?php echo Text::_('COM_POWERHOUSECMS_CONTENT_ITEMS'); ?></p>
                        <a href="<?php echo Route::_('index.php?option=com_powerhousecms&view=contents'); ?>" class="btn btn-primary">
                            <?php echo Text::_('COM_POWERHOUSECMS_MANAGE_CONTENT'); ?>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h2 class="card-title text-success"><?php echo $this->stats['media_count']; ?></h2>
                        <p class="card-text"><?php echo Text::_('COM_POWERHOUSECMS_MEDIA_FILES'); ?></p>
                        <a href="<?php echo Route::_('index.php?option=com_powerhousecms&view=media'); ?>" class="btn btn-success">
                            <?php echo Text::_('COM_POWERHOUSECMS_MANAGE_MEDIA'); ?>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h2 class="card-title text-info"><?php echo $this->stats['template_count']; ?></h2>
                        <p class="card-text"><?php echo Text::_('COM_POWERHOUSECMS_TEMPLATES'); ?></p>
                        <a href="<?php echo Route::_('index.php?option=com_powerhousecms&view=templates'); ?>" class="btn btn-info">
                            <?php echo Text::_('COM_POWERHOUSECMS_MANAGE_TEMPLATES'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Content -->
        <?php if (!empty($this->stats['recent_content'])) : ?>
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <?php echo Text::_('COM_POWERHOUSECMS_RECENT_CONTENT'); ?>
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th><?php echo Text::_('COM_POWERHOUSECMS_TITLE'); ?></th>
                                        <th><?php echo Text::_('COM_POWERHOUSECMS_TYPE'); ?></th>
                                        <th><?php echo Text::_('COM_POWERHOUSECMS_STATUS'); ?></th>
                                        <th><?php echo Text::_('COM_POWERHOUSECMS_AUTHOR'); ?></th>
                                        <th><?php echo Text::_('COM_POWERHOUSECMS_MODIFIED'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($this->stats['recent_content'] as $item) : ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item->title); ?></td>
                                        <td>
                                            <span class="badge badge-secondary">
                                                <?php echo htmlspecialchars($item->type); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($item->status === 'published') : ?>
                                                <span class="badge badge-success"><?php echo Text::_('COM_POWERHOUSECMS_PUBLISHED'); ?></span>
                                            <?php elseif ($item->status === 'draft') : ?>
                                                <span class="badge badge-warning"><?php echo Text::_('COM_POWERHOUSECMS_DRAFT'); ?></span>
                                            <?php else : ?>
                                                <span class="badge badge-secondary"><?php echo htmlspecialchars($item->status); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($item->author); ?></td>
                                        <td><?php echo HTMLHelper::_('date', $item->modified, Text::_('DATE_FORMAT_LC4')); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- PowerhouseCMS Integration Info -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <?php echo Text::_('COM_POWERHOUSECMS_INTEGRATION_INFO'); ?>
                        </h3>
                    </div>
                    <div class="card-body">
                        <p><?php echo Text::_('COM_POWERHOUSECMS_INTEGRATION_DESCRIPTION'); ?></p>
                        <div class="row">
                            <div class="col-md-6">
                                <h5><?php echo Text::_('COM_POWERHOUSECMS_FEATURES'); ?></h5>
                                <ul>
                                    <li><?php echo Text::_('COM_POWERHOUSECMS_FEATURE_CONTENT_SYNC'); ?></li>
                                    <li><?php echo Text::_('COM_POWERHOUSECMS_FEATURE_MEDIA_MANAGEMENT'); ?></li>
                                    <li><?php echo Text::_('COM_POWERHOUSECMS_FEATURE_TEMPLATE_SYSTEM'); ?></li>
                                    <li><?php echo Text::_('COM_POWERHOUSECMS_FEATURE_API_INTEGRATION'); ?></li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5><?php echo Text::_('COM_POWERHOUSECMS_QUICK_ACTIONS'); ?></h5>
                                <div class="btn-group-vertical d-grid gap-2">
                                    <a href="<?php echo Route::_('index.php?option=com_powerhousecms&task=dashboard.syncContent'); ?>" 
                                       class="btn btn-outline-primary">
                                        <i class="fas fa-sync"></i> <?php echo Text::_('COM_POWERHOUSECMS_SYNC_CONTENT'); ?>
                                    </a>
                                    <a href="<?php echo Route::_('index.php?option=com_powerhousecms&view=contents&layout=edit'); ?>" 
                                       class="btn btn-outline-success">
                                        <i class="fas fa-plus"></i> <?php echo Text::_('COM_POWERHOUSECMS_CREATE_CONTENT'); ?>
                                    </a>
                                    <a href="<?php echo Route::_('index.php?option=com_config&component=com_powerhousecms'); ?>" 
                                       class="btn btn-outline-secondary">
                                        <i class="fas fa-cog"></i> <?php echo Text::_('COM_POWERHOUSECMS_SETTINGS'); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($this->sidebar)) : ?>
        </div>
    <?php endif; ?>
</div>
