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
use Da\AuthCommonBundle\Exception\InvalidApiTokenException;
use Da\ApiClientBundle\HttpClient\RestApiClientBridge;
use Da\AuthCommonBundle\Model\ClientManagerInterface;
use Da\ApiClientBundle\HttpClient\RestApiClientImplementorInterface;

/**
 * ClientManager is an implementation of a client manager
 * where you retrieve a client from a webservice.
 *
 * @author Thomas Prelot
 */
class ClientManager extends RestApiClientBridge implements ClientManagerInterface
{
    /**
     * The client entity class.
     *
     * @var string
     */
    protected $class;
    
    /**
     * Constructor.
     *
     * @param RestApiClientImplementorInterface $implementor
     * @param array                             $configuration
     * @param string                            $class         Client entity class.
     */
    public function __construct(RestApiClientImplementorInterface $implementor, array $configuration, $class)
    {
        $this->class = $class;

        parent::__construct($implementor, $configuration);
    }

    /**
     * {@inheritdoc}
     */
    public function retrieveClientByApiToken($apiToken)
    {
        $client = new $this->class();

        try {
            $clientArray = json_decode($this->get(sprintf('/clients/%s', $apiToken), array(), array('Accept' => 'application/json')), true);

            $client
                ->setName($clientArray['name'])
            ;
        }
        catch (ApiHttpResponseException $e) {
            if (404 === $e->getHttpCode()) {
                throw new InvalidApiTokenException();
            } else {
                throw $e;
            }
        }

        return $client;
    }
}
