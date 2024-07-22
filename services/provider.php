<?php
/**
 * @package    JLSitemap - Phoca Download Plugin
 * @version    1.0.0
 * @author     Sergey Tolkachyov - web-tolk.ru
 * @copyright  Copyright (c) 2024 Sergey Tolkachyov. All rights reserved.
 * @license    GNU General Public License v3.0
 * @link       https://web-tolk.ru/dev/joomla-plugins/jlsitemap-phoca-download-joomla-plugin
 */

defined('_JEXEC') || die;

use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;
use Joomla\Plugin\Jlsitemap\Phocadownload\Extension\Phocadownload;

return new class () implements ServiceProviderInterface {
    /**
     * Registers the service provider with a DI container.
     *
     * @param   Container  $container  The DI container.
     *
     * @return  void
     *
     * @since   4.0.0
     */
    public function register(Container $container)
    {
        $container->set(
            PluginInterface::class,
            function (Container $container) {
                $subject = $container->get(DispatcherInterface::class);
                $config  = (array) PluginHelper::getPlugin('jlsitemap', 'phocadownload');
                $plugin = new Phocadownload($subject, $config);
                $plugin->setApplication(Factory::getApplication());
                $plugin->setDatabase($container->get('DatabaseDriver'));
                return $plugin;
            }
        );
    }
};