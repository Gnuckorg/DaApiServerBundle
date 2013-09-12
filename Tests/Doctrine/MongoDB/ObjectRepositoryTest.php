<?php

/*
 * This file is part of the Da Project.
 *
 * (c) Thomas Prelot <tprelot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Da\ApiServerBundle\Tests\Doctrine\MongoDB;

use Da\ApiServerBundle\Doctrine\MongoDB\ObjectRepository;
use Da\ApiServerBundle\Model\AbstractQueryBuilderDecorator;

/**
 * @author Thomas Prelot
 */
class ObjectRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Da\ApiServerBundle\Doctrine\MongoDB\ObjectRepository::addDecoratorClassName
     * @covers Da\ApiServerBundle\Doctrine\MongoDB\ObjectRepository::getDecorators
     * @covers Da\ApiServerBundle\Doctrine\MongoDB\ObjectRepository::setDecoratorDirectory
     * @covers Da\ApiServerBundle\Doctrine\MongoDB\ObjectRepository::getDecoratorNamespace
     * @covers Da\ApiServerBundle\Doctrine\MongoDB\ObjectRepository::getDecoratorDirectory
     * @covers Da\ApiServerBundle\Doctrine\MongoDB\ObjectRepository::createQueryBuilder
     */
    public function testCreateQueryBuilder()
    {
        $queryBuilder = $this->getMockBuilder('Doctrine\MongoDB\Query\Builder')
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock()
        ;

        ObjectRepository::setDecoratorDirectory(__DIR__.'/../../Fixtures/Model/Decorator', '\Da\ApiServerBundle\Tests\Fixtures\Model\Decorator');
        $objectRepository = $this->getMockBuilder('Da\ApiServerBundle\Doctrine\MongoDB\ObjectRepository')
            ->disableOriginalConstructor()
            ->setMethods(array('getNativeQueryBuilder', 'getClassMetaData'))
            ->getMock()
        ;
        $objectRepository
            ->expects($this->any())
            ->method('getNativeQueryBuilder')
            ->will($this->returnValue($queryBuilder))
        ;
        $objectRepository
            ->expects($this->any())
            ->method('getClassMetaData')
            ->will($this->returnValue(array('fieldMappings' => array(array('type' => 'string')))));
        ;
        
        $decoratedQueryBuilder = $objectRepository->createQueryBuilder();

        $this->assertEquals('a', $decoratedQueryBuilder->a(), 
            '->createQueryBuilder() returns a decorated query builder with all the decorators from the "getDecoratorDirectory()" directory.'
        );
        $this->assertEquals('b', $decoratedQueryBuilder->b(), 
            '->createQueryBuilder() returns a decorated query builder with all the decorators from the "getDecoratorDirectory()" directory.'
        );
    }
}