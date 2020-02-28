<?php

namespace RichCongress\NormalizerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This class contains the configuration information for the bundle.
 */
final class Configuration implements ConfigurationInterface
{
    public const PREFIX = 'rich_congress_normalizer';

    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder(self::PREFIX);
        $rootNode = \method_exists(TreeBuilder::class, 'getRootNode')
            ? $treeBuilder->getRootNode()
            : $treeBuilder->root(self::PREFIX);

        $rootNode
            ->children()
                ->booleanNode('virtual_property')->defaultValue(true)->end()
            ->end();

        return $treeBuilder;
    }
}
