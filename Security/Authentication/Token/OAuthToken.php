<?php

/**
 * This file is part of the Da Project.
 *
 * (c) Thomas Prelot <tprelot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Da\ApiServerBundle\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

/**
 * OAuthToken class.
 *
 * @author Thomas Prelot
 */
class OAuthToken extends AbstractToken
{
    /**
     * @var string
     */
    protected $token;

    /**
     * @var string
     */
    protected $apiToken;

    public function setToken($token)
    {
        $this->token = $token;
    }

    public function getToken()
    {
        return $this->token;
    }
    
    public function getCredentials()
    {
        return $this->token;
    }
}
