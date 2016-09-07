<?php

namespace Elcodi\Admin\SocialBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

use Elcodi\Bundle\CoreBundle\DependencyInjection\Abstracts\AbstractConfiguration;
/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration extends AbstractConfiguration implements ConfigurationInterface
{
    // /**
    //  * {@inheritdoc}
    //  */
    // public function getConfigTreeBuilder()
    // {
    //     $treeBuilder = new TreeBuilder();
    //     $rootNode = $treeBuilder->root('elcodi_tonki');

    //     // Here you should define the parameters that are allowed to
    //     // configure your bundle. See the documentation linked above for
    //     // more information on that topic.

    //     return $treeBuilder;
    // }

    /**
     * Configure the root node
     *
     * @param ArrayNodeDefinition $rootNode
     */
    protected function setupTree(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('mapping')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->append($this->addMappingNode(
                            'social',
                            'Elcodi\Admin\SocialBundle\Entity\Social',
                            '@AdminSocialBundle/Resources/config/doctrine/Social.orm.yml',
                            'default',
                            true
                        ))
                    ->end()
                ->end()
            ->end();
    }
}
