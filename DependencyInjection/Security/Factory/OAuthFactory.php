<?php

/**
 * This file is part of the Da Project.
 *
 * (c) Thomas Prelot <tprelot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Da\ApiServerBundle\DependencyInjection\Security\Factory;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

/**
 * OAuthFactory class.
 *
 * @author Thomas Prelot
 */
class OAuthFactory implements SecurityFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
        $providerId = 'security.authentication.provider.da_api_server.oauth.'.$id;
        $container
            ->setDefinition($providerId, new DefinitionDecorator('da_api_server.security.authentication.provider.oauth'))
            ->replaceArgument(0, new Reference($userProvider))
            ->addTag('da_api_server.firewall.oauth')
        ;

        $listenerId = 'security.authentication.listener.da_api_server.oauth.'.$id;
        $container->setDefinition($listenerId, new DefinitionDecorator('da_api_server.security.authentication.listener.oauth'));

        return array($providerId, $listenerId, $defaultEntryPoint);
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition()
    {
        return 'pre_auth';
    }

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return 'da_api_oauth';
    }

    /**
     * {@inheritdoc}
     */
    public function addConfiguration(NodeDefinition $node)
    {
    }
}
