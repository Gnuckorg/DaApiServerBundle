<?php

/**
 * This file is part of the Da Project.
 *
 * (c) Thomas Prelot <tprelot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Da\ApiServerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * InjectSecurityFirewallParametersCompilerPass allow to inject the security firewalls' parameters.
 *
 * @author Thomas Prelot
 */
class InjectSecurityFirewallParametersCompilerPass implements CompilerPassInterface
{
    /**
     * Process the ContainerBuilder to inject the configuration and the implementor
     * into the API client.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        // Client manager.
        $taggedServices = $container->findTaggedServiceIds(
            'da_api_server.firewall.api'
        );
        $clientManagerId = $container->getParameter('da_api_server.client_manager');

        if (null === $clientManagerId) {
            if ($container->hasDefinition('da_oauth_server.client_manager.doctrine')) {
                $clientManagerId = 'da_oauth_server.client_manager.doctrine';
            } else {
                $clientManagerId = 'da_api_server.client_manager.http';
            }
        }

        foreach ($taggedServices as $id => $attributes) {
            $container->getDefinition($id)->replaceArgument(1, new Reference($clientManagerId));
        }

        // User manager.
        $taggedServices = $container->findTaggedServiceIds(
            'da_api_server.firewall.oauth'
        );
        $userManagerId = $container->getParameter('da_api_server.user_manager');

        if (null === $userManagerId) {
            if ($container->hasDefinition('da_oauth_server.user_manager.doctrine')) {
                $userManagerId = 'da_oauth_server.user_manager.doctrine';
            } else {
                $userManagerId = 'da_api_server.user_manager.http';
            }
        }

        foreach ($taggedServices as $id => $attributes) {
            $container->getDefinition($id)->replaceArgument(1, new Reference($userManagerId));
        }
    }
}
