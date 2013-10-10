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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Da\ApiServerBundle\Security\Authentication\Token\OAuthToken;
use Da\AuthCommonBundle\Exception\OAuthTokenNotFoundException;

/**
 * OAuthListener class.
 *
 * @author Thomas Prelot
 */
class OAuthListener implements ListenerInterface
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
        parent::__construct($securityContext, $authenticationManager);
    }

    /**
     * @param GetResponseEvent $event The event.
     */
    public function handle(GetResponseEvent $event)
    {
        try {
            if (null === $oAuthToken = $this->getAccessTokenFromHeaders($event->getRequest(), true)) {
                throw new OAuthTokenNotFoundException();
            }

            $token = new OAuthToken();
            $token->setToken($oAuthToken);
        
            $returnValue = $this->authenticationManager->authenticate($token);

            if ($returnValue instanceof TokenInterface) {
                return $this->securityContext->setToken($returnValue);
            }

            if ($returnValue instanceof Response) {
                return $event->setResponse($returnValue);
            }
        } catch (AuthenticationException $e) {
            if ($e instanceof OAuthTokenNotFoundException) {
                $event->setResponse(new Response($e->getMessageKey(), 401));
            } else {
                $event->setResponse(new Response($e->getMessageKey(), 403));
            }
        }
    }

    /**
     * Get the access token from the header.
     * Credits to Tim Ridgely <tim.ridgely@gmail.com>.
     *
     * @param Request $request           The request.
     * @param boolean $removeFromRequest Should remove the token form the request?
     *
     * @return string The (bearer) access token or null if non-existent.
     */
    protected function getAccessTokenFromHeaders(Request $request, $removeFromRequest)
    {
        $header = null;
        if (!$request->headers->has('AUTHORIZATION')) {
            // The Authorization header may not be passed to PHP by Apache;
            // Trying to obtain it through apache_request_headers()
            if (function_exists('apache_request_headers')) {
                $headers = apache_request_headers();

                // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
                $headers = array_combine(array_map('ucwords', array_keys($headers)), array_values($headers));

                if (isset($headers['Authorization'])) {
                    $header = $headers['Authorization'];
                }
            }
        } else {
          $header = $request->headers->get('AUTHORIZATION');
        }

        if (!$header) {
            return NULL;
        }

        if (!preg_match('/'.preg_quote(self::TOKEN_BEARER_HEADER_NAME, '/').'\s(\S+)/', $header, $matches)) {
            return NULL;
        }

        $token = $matches[1];

        if ($removeFromRequest) {
            $request->headers->remove('AUTHORIZATION');
        }

        return $token;
    }
}
