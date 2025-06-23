<?php
/**
 * @package     PowerhouseCMS
 * @subpackage  com_powerhousecms
 * @copyright   Copyright (C) 2024 PowerhouseCMS. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use PowerhouseCMS\Component\PowerhouseCMS\Administrator\Extension\PowerhouseCMSComponent;

/**
 * The PowerhouseCMS service provider.
 *
 * @since  1.0.0
 */
return new class implements ServiceProviderInterface
{
    /**
     * Registers the service provider with a DI container.
     *
     * @param   Container  $container  The DI container.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function register(Container $container)
    {
        $container->registerServiceProvider(new MVCFactory('\\PowerhouseCMS\\Component\\PowerhouseCMS'));
        $container->registerServiceProvider(new ComponentDispatcherFactory('\\PowerhouseCMS\\Component\\PowerhouseCMS'));

        $container->set(
            ComponentInterface::class,
            function (Container $container) {
                $component = new PowerhouseCMSComponent($container->get(ComponentDispatcherFactoryInterface::class));
                $component->setMVCFactory($container->get(MVCFactoryInterface::class));

                return $component;
            }
        );
    }
};
