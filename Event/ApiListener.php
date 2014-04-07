<?php

/**
 * This file is part of the Da Project.
 *
 * (c) Thomas Prelot <tprelot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Da\ApiServerBundle\Event;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * Set headers related to the API processing.
 *
 * @author Thomas Prelot <tprelot@gmail.com>
 */
class ApiListener
{
    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $request = $event->getRequest();
        $response = $event->getResponse();

        $userAgent = $request->headers->get('User-Agent');
        
        if (0 === strpos($userAgent, 'DaApiClient')) {
            $response->headers->set('X-Da-Agent', 'DaApiServer');
        }
    }
}