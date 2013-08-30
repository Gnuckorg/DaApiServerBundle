<?php

/*
 * This file is part of the Da Project.
 *
 * (c) Thomas Prelot <tprelot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Da\DiBundle\Tests\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Da\ApiServerBundle\Doctrine\MongoDB\ObjectRepository;
use Da\ApiServerBundle\Model\AbstractQueryBuilderDecorator;

/**
 * @author Thomas Prelot
 */
class EqualsTest extends \PHPUnit_Framework_TestCase
{
    protected $match;

    public function getValue()
    {
        $values = array();

        $values[] = array('abc', array('abc'));
        $values[] = array('=##abd', array('abd'));
        //$values[] = array('=##abc||=##bca');
        //$values[] = array('=##abc&&=##2&&=##234d_s_d');
        // TODO: test bad values.

        return $values;
    }

    public function getBadValue()
    {
        $values = array();

        $values[] = array('=##abd##oic');

        return $values;
    }

    public function getDecoratorMock()
    {
        $decorator = $this->getMockBuilder('Da\ApiServerBundle\Doctrine\MongoDB\Decorator\Equals')
            ->disableOriginalConstructor()
            ->setMethods(array('equals', 'addOr'))
            ->getMock()
        ;
        $decorator
            ->expects($this->any())
            ->method('equals')
            ->will($this->returnCallback(array($this, 'addMatchedValue')))
        ;
        $decorator
            ->expects($this->any())
            ->method('addOr')
            ->will($this->returnCallback(array($this, 'addMatchedValue')))
        ;

        return $decorator;
    }

    /**
     * @covers Da\ApiServerBundle\Model\AbstractQueryBuilderDecorator::match
     * @covers Da\ApiServerBundle\Model\AbstractQueryBuilderDecorator::parse
     * @covers Da\ApiServerBundle\Doctrine\MongoDB\Decorator\Equals::handle
     * @covers Da\ApiServerBundle\Doctrine\MongoDB\Decorator\Equals::check
     * @covers Da\ApiServerBundle\Doctrine\MongoDB\Decorator\Equals::build
     * @dataProvider getValue
     */
    public function testMatch($value, $expected)
    {
        $this->match = array();

        $decorator = $this->getDecoratorMock();
        $decorator->match($value);

        $this->assertEquals($expected, $this->match, 
            '->match() redirect the value in the equals method.'
        );
    }

    /**
     * @covers Da\ApiServerBundle\Model\AbstractQueryBuilderDecorator::match
     * @covers Da\ApiServerBundle\Model\AbstractQueryBuilderDecorator::parse
     * @covers Da\ApiServerBundle\Doctrine\MongoDB\Decorator\Equals::handle
     * @covers Da\ApiServerBundle\Doctrine\MongoDB\Decorator\Equals::check
     * @dataProvider getBadValue
     * @expectedException InvalidArgumentException
     */
    public function testMatchBadArguments($value)
    {
        $this->match = array();

        $decorator = $this->getDecoratorMock();
        $decorator->match($value);
    }

    public function addMatchedValue($value = '')
    {
        $this->match[] = $value;
    }
}