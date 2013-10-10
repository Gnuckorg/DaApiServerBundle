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
use Da\ApiServerBundle\Security\Authentication\Token\OAuthToken;
use Da\AuthCommonBundle\Model\UserManagerInterface;
use Da\AuthCommonBundle\Exception\InvalidAccessTokenException;

/**
 * OAuthProvider class.
 *
 * @author Thomas Prelot
 */
class OAuthProvider implements AuthenticationProviderInterface
{
    /**
     * The user provider.
     *
     * @var \Symfony\Component\Security\Core\User\UserProviderInterface
     */
    protected $userProvider;

    /**
     * The user manager.
     *
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * @param UserProviderInterface  $userProvider  The user provider.
     * @param UserManagerInterface $userManager The user manager.
     */
    public function __construct(UserProviderInterface $userProvider, UserManagerInterface $userManager)
    {
        $this->userProvider  = $userProvider;
        $this->userManager = $userManager;
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
        $user = $this->userManager->findUserByAccessToken($tokenString);

        $token = new OAuthToken($user->getRoles());
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
