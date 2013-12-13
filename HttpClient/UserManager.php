<?php

/**
 * This file is part of the Da Project.
 *
 * (c) Thomas Prelot <tprelot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Da\ApiServerBundle\HttpClient;

use Doctrine\Common\Persistence\ManagerRegistry;
use Da\AuthCommonBundle\Exception\InvalidAccessTokenException;
use Da\ApiClientBundle\HttpClient\RestApiClientBridge;
use Da\AuthCommonBundle\Model\UserManagerInterface;
use Da\ApiClientBundle\HttpClient\RestApiClientImplementorInterface;

/**
 * UserManager is an implementation of a user manager
 * where you retrieve a user from a webservice.
 *
 * @author Thomas Prelot
 */
class UserManager extends RestApiClientBridge implements UserManagerInterface
{
    /**
     * The user entity class.
     *
     * @var string
     */
    protected $class;
    
    /**
     * Constructor.
     *
     * @param RestApiClientImplementorInterface $implementor
     * @param array                             $configuration
     * @param string                            $class         User entity class.
     */
    public function __construct(RestApiClientImplementorInterface $implementor, array $configuration, $class)
    {
        $this->class = $class;

        parent::__construct($implementor, $configuration);
    }
    
    /**
     * {@inheritdoc}
     */
    public function retrieveUserByAccessToken($accessToken)
    {
        $user = new $this->class();

        try {
            $userArray = json_decode($this->get(sprintf('/accesstokens/%s/user', $accessToken), array(), array('Accept' => 'application/json')), true);
            $user
                ->setId($userArray['id'])
                ->setUsername($userArray['username'])
                ->setEmail($userArray['email'])
            ;
        }
        catch (ApiHttpResponseException $e) {
            if (404 === $e->getHttpCode()) {
                throw new InvalidAccessTokenException();
            } else {
                throw $e;
            }
        }

        return $user;
    }
}
