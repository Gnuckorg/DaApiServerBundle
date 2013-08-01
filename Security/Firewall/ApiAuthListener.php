<?php

/**
 * This file is part of the Da Project.
 *
 * (c) Thomas Prelot <tprelot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Da\ApiServerBundle\Security\Firewall;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\HttpFoundation\Request;
use Da\ApiServerBundle\Security\Authentication\Token\ApiToken;

/**
 * ApiAuthListener class.
 *
 * @author Thomas Prelot
 */
class ApiAuthListener implements ListenerInterface
{
    /**
     * The security context.
     *
     * @var SecurityContextInterface
     */
    protected $securityContext;

    /**
     * The authentication manager.
     *
     * @var AuthenticationManagerInterface
     */
    protected $authenticationManager;

    /**
     * @param SecurityContextInterface       $securityContext       The security context.
     * @param AuthenticationManagerInterface $authenticationManager The authentication manager.
     */
    public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager)
    {
        $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
    }

    /**
     * @param GetResponseEvent $event The event.
     */
    public function handle(GetResponseEvent $event)
    {
        if (null === $apiToken = $this->getApiTokenFromHeaders($event->getRequest(), true)) {
            return;
        }

        $token = new ApiToken();
        $token->setToken($apiToken);

        try {
            $returnValue = $this->authenticationManager->authenticate($token);

            if ($returnValue instanceof TokenInterface) {
                return $this->securityContext->setToken($returnValue);
            }

            if ($returnValue instanceof Response) {
                return $event->setResponse($returnValue);
            }
        } catch (AuthenticationException $e) {
            if (null !== $p = $e->getPrevious()) {
                $event->setResponse($p->getHttpResponse());
            }
        }
    }

    /**
     * Get the API token from the header.
     *
     * @param Request $request           The request.
     * @param boolean $removeFromRequest Should remove the token form the request?
     *
     * @return string The API token or null if non-existent.
     */
    protected function getApiTokenFromHeaders(Request $request, $removeFromRequest)
    {
        $token = null;
        if (!$request->headers->has('X-API-Token')) {
            // The Authorization header may not be passed to PHP by Apache.
            // Trying to obtain it through apache_request_headers().
            if (function_exists('apache_request_headers')) {
                $headers = apache_request_headers();

                if (isset($headers['X-API-Token'])) {
                   $token = $headers['X-API-Token'];
                }
            }
        } else {
            $token = $request->headers->get('X-API-Token');
        }

        if (!$token) {
            return null;
        }

        if ($removeFromRequest) {
            $request->headers->remove('X-API-Token');
        }

        return $token;
    }
}
