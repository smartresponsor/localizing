<?php

declare(strict_types=1);

namespace App\Localizing\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class LocalizingExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(\dirname(__DIR__, 2).'/config'));
        $loader->load('services.yaml');

        $container->setParameter('localizing.package_dir', \dirname(__DIR__, 2));
    }
}
