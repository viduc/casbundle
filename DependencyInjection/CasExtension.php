<?php declare(strict_types=1);
/******************************************************************************/
/*                                  CASBUNDLE                                 */
/*     Auteur: Tristan Fleury - https://github.com/viduc - viduc@mail.fr      */
/*                              Licence: Apache-2.0                           */
/******************************************************************************/

namespace Viduc\CasBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Viduc\CasBundle\Security\CasAuthenticator;
use Viduc\CasBundle\Security\UserProvider;

class CasExtension extends Extension
{

    final public function load(array $configs, ContainerBuilder $container) : void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . DIRECTORY_SEPARATOR . '..'
                . DIRECTORY_SEPARATOR . 'Resources' . DIRECTORY_SEPARATOR
            . 'config')
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
