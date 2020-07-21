<?php


namespace Viduc\CasBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Viduc\CasBundle\Security\CasAuthenticator;
use Viduc\CasBundle\Security\UserProvider;

class CasExtension extends Extension
{

    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('services.yaml');
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $authenticator = $container->autowire(
            'viduc.cas_authenticator',
            CasAuthenticator::class
        );
        $authenticator->setArguments(array($config));
        $container->register(
            'viduc.cas_user_provider',
            UserProvider::class
        );
    }
}
