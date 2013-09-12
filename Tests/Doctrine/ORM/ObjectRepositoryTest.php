<?php

/*
 * This file is part of the Da Project.
 *
 * (c) Thomas Prelot <tprelot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Da\ApiServerBundle\Tests\Doctrine\ORM;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Da\ApiServerBundle\Doctrine\ORM\ObjectRepository;
use Da\ApiServerBundle\Model\AbstractQueryBuilderDecorator;

/**
 * @author Thomas Prelot
 */
class ObjectRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Da\ApiServerBundle\Doctrine\ORM\ObjectRepository::addDecoratorClassName
     * @covers Da\ApiServerBundle\Doctrine\ORM\ObjectRepository::getDecorators
     * @covers Da\ApiServerBundle\Doctrine\ORM\ObjectRepository::setDecoratorDirectory
     * @covers Da\ApiServerBundle\Doctrine\ORM\ObjectRepository::getDecoratorNamespace
     * @covers Da\ApiServerBundle\Doctrine\ORM\ObjectRepository::getDecoratorDirectory
     * @covers Da\ApiServerBundle\Doctrine\ORM\ObjectRepository::createQueryBuilder
     */
    public function testCreateQueryBuilder()
    {
        $queryBuilder = $this->getMockBuilder('Doctrine\MongoDB\Query\Builder')
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock()
        ;

        ObjectRepository::setDecoratorDirectory(__DIR__.'/../../Fixtures/Model/Decorator', '\Da\ApiServerBundle\Tests\Fixtures\Model\Decorator');
        $objectRepository = $this->getMockBuilder('Da\ApiServerBundle\Doctrine\ORM\ObjectRepository')
            ->disableOriginalConstructor()
            ->setMethods(array('getNativeQueryBuilder', 'getClassMetaData'))
            ->getMock()
        ;
        $objectRepository
            ->expects($this->any())
            ->method('getNativeQueryBuilder')
            ->will($this->returnValue($queryBuilder))
        ;
        $classMetaData = new ClassMetadataInfo('dumb');
        $objectRepository
            ->expects($this->any())
            ->method('getClassMetaData')
            ->will($this->returnValue($classMetaData));
        ;
        
        $decoratedQueryBuilder = $objectRepository->createQueryBuilder('dumb');

        $this->assertEquals('a', $decoratedQueryBuilder->a(), 
            '->createQueryBuilder() returns a decorated query builder with all the decorators from the "getDecoratorDirectory()" directory.'
        );
        $this->assertEquals('b', $decoratedQueryBuilder->b(), 
            '->createQueryBuilder() returns a decorated query builder with all the decorators from the "getDecoratorDirectory()" directory.'
        );
    }
}