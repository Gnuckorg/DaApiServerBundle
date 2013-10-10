<?php

/**
 * This file is part of the Da Project.
 *
 * (c) Thomas Prelot <tprelot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Da\ApiServerBundle\Security\Authentication\Provider;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Da\ApiServerBundle\Security\Authentication\Token\ApiToken;
use Da\AuthCommonBundle\Model\ClientManagerInterface;
use Da\AuthCommonBundle\Exception\InvalidApiTokenException;

/**
 * ApiAuthProvider class.
 *
 * @author Thomas Prelot
 */
class ApiAuthProvider implements AuthenticationProviderInterface
{
    /**
     * The user provider.
     *
     * @var \Symfony\Component\Security\Core\User\UserProviderInterface
     */
    protected $userProvider;

    /**
     * The client manager.
     *
     * @var ClientManagerInterface
     */
    protected $clientManager;

    /**
     * @param UserProviderInterface  $userProvider  The user provider.
     * @param ClientManagerInterface $clientManager The client manager.
     */
    public function __construct(UserProviderInterface $userProvider, ClientManagerInterface $clientManager)
    {
        $this->userProvider  = $userProvider;
        $this->clientManager = $clientManager;
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate(TokenInterface $token)
    {
        if (!$this->supports($token)) {
            return null;
        }

        $tokenString = $token->getToken();
        $client = $this->clientManager->retrieveClientByApiToken($tokenString);
        $scope = $client->getScope();

        $roles = array();
        if (!empty($scope)) {
            foreach (explode(' ', $scope) as $role) {
                $roles[] = 'ROLE_' . strtoupper($role);
            }
        }

        $token = new ApiToken($roles);
        $token->setAuthenticated(true);
        $token->setToken($tokenString);

        return $token;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(TokenInterface $token)
    {
        return $token instanceof ApiToken;
    }
}
