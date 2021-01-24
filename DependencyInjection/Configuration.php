<?php declare(strict_types=1);
/******************************************************************************/
/*                                  CASBUNDLE                                 */
/*     Auteur: Tristan Fleury - https://github.com/viduc - viduc@mail.fr      */
/*                              Licence: Apache-2.0                           */
/******************************************************************************/

namespace Viduc\CasBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    final public function getConfigTreeBuilder() : TreeBuilder
    {
        $treeBuilder = new TreeBuilder('Viduc_cas');
        //fix deprecated call
        if(Kernel::VERSION > 4.2) {
            $rootNode = $treeBuilder->getRootNode();
        }
        else {
            $rootNode = $treeBuilder->root('cas');
        }
        $rootNode
            ->children()
                ->scalarNode('host')
                ->end()
                ->scalarNode('port')
                    ->defaultValue(443)
                ->end()
                ->scalarNode('uri')
                    ->defaultValue('')
                ->end()
                ->scalarNode('version')
                    ->defaultValue('2.0')
                ->end()
            ->end()
        ;
        return $treeBuilder;
    }
}
