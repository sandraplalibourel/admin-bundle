<?php

namespace Aropixel\AdminBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class AropixelAdminExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $this->registerParameters($container, $config);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('form.yml');
    }


    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param array                                                   $config
     */
    public function registerParameters(ContainerBuilder $container, array $config)
    {
        //$config['filter_sets']['admin_thumbnail']['label'] = "Vignette d'administration";
        foreach ($config['filter_sets'] as $className => $filters) {
            $config['filter_sets'][$className]['admin_thumbnail'] = "Vignette d'administration";
        }
        $container->setParameter('aropixel_admin.filter_sets', $config['filter_sets']);
        $container->setParameter('aropixel_admin.editor_filter_sets', $config['editor_filter_sets']);
        $container->setParameter('aropixel_admin.copyright', $config['copyright']);
        $container->setParameter('aropixel_admin.client', $config['client']);
        $container->setParameter('aropixel_admin.theme', $config['theme']);
        $container->setParameter('aropixel_admin.forms', $config['forms']);
        $container->setParameter('aropixel_admin.entities', $config['entities']);

        if (isset($config['form_translations'])) {
//            $container->setParameter('aropixel_admin.form_translations.locales', $config['form_translations']['locales']);
//            $container->setParameter('aropixel_admin.form_translations.default_locale', $config['form_translations']['default_locale'] ?: $container->getParameter('kernel.default_locale', 'fr'));
        }
        else {
            $container->setParameter('aropixel_admin.form_translations.locales', array());
        }

    }



    public function prepend(ContainerBuilder $container)
    {
        // get all bundles
        $bundles = $container->getParameter('kernel.bundles');
        $config = array(
            'admin_thumbnail' => array(
                'quality' => 75,
                'filters' => array(
                    'upscale_thumbnail' => array(
                        'height' => 400,
                        'width' => 400,
                    ),
                )
            ),
            'admin_preview' => array(
                'quality' => 75,
                'filters' => array(
                    'relative_resize' => array(
                        'widen' => 800,
                    ),
                )
            ),
            'admin_crop' => array(
                'quality' => 75,
                'filters' => array(
                    'relative_resize' => array(
                        'widen' => 600,
                    ),
                )
            )
        );

        $sizes = array(100, 200, 300, 400, 600, 800);
        foreach ($sizes as $width) {
            $config['editor_'.$width] = array(
                'quality' => 75,
                'filters' => array(
                    'relative_resize' => array(
                        'widen' => $width
                    ),
                )
            );

        }

        $config['editor_100pc'] = array(
            'quality' => 75,
            'filters' => array(
                'relative_resize' => array(
                    'widen' => 1200
                ),
            )
        );

        $config['auto'] = array(
            'quality' => 75,
            'filters' => array(
                'relative_resize' => array(
                    'widen' => 100
                ),
            )
        );


        $container->prependExtensionConfig('liip_imagine', array('filter_sets' => $config));

    }

}
