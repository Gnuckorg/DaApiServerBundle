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
        $taggedServices = $container->findTaggedServiceIds(
            'da_api_server.firewall.api'
        );
        foreach ($taggedServices as $id => $attributes) {
            $container->getDefinition($id)->replaceArgument(1, new Reference($container->getParameter('da_api_server.client_manager')));
        }

        $taggedServices = $container->findTaggedServiceIds(
            'da_api_server.firewall.oauth'
        );
        foreach ($taggedServices as $id => $attributes) {
            $container->getDefinition($id)->replaceArgument(1, new Reference($container->getParameter('da_api_server.user_manager')));
        }
    }
}
