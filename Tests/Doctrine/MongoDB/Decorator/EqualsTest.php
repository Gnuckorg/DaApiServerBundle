<?php

/*
 * This file is part of the Da Project.
 *
 * (c) Thomas Prelot <tprelot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Da\ApiServerBundle\Tests\Doctrine\MongoDB\Decorator;

use Doctrine\MongoDB\Query\Expr;
use Da\ApiServerBundle\Doctrine\MongoDB\ObjectRepository;
use Da\ApiServerBundle\Model\AbstractQueryBuilderDecorator;

/**
 * @author Thomas Prelot
 */
class EqualsTest extends \PHPUnit_Framework_TestCase
{
    protected $match;
    protected $association;

    public function getValue()
    {
        $values = array();

        $values[] = array('abc', 'and', array('abc'));
        $values[] = array('=~~abd', 'and', array('abd'));
        $values[] = array('abc~*~bda', 'or', array('abc', 'bda'));
        $values[] = array('=~~abc~*~=~~bca', 'or', array('abc', 'bca'));
        $values[] = array('=~~abc~-~=~~2~-~=~~234d_s_d', 'and', array('abc', '2', '234d_s_d'));

        return $values;
    }

    public function getBadValue()
    {
        $values = array();

        $values[] = array('=~~abd~~oic');

        return $values;
    }

    public function getDecoratorMock()
    {
        $decorator = $this->getMockBuilder('Da\ApiServerBundle\Doctrine\MongoDB\Decorator\Equals')
            ->disableOriginalConstructor()
            ->setMethods(array('expr', 'addAnd', 'translate'))
            ->getMock()
        ;
        $decorator
            ->expects($this->any())
            ->method('expr')
            ->will($this->returnCallback(array($this, 'expr')))
        ;
        $decorator
            ->expects($this->any())
            ->method('addAnd')
            ->will($this->returnCallback(array($this, 'addMatchedValue')))
        ;
        $decorator
            ->expects($this->any())
            ->method('translate')
            ->will($this->returnArgument(0))
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
    public function testMatch($value, $association, $expected)
    {
        $this->match = array();
        $this->association = $association;

        $decorator = $this->getDecoratorMock();
        $decorator->match('dumb', $value);

        $this->assertEquals($expected, $this->match, 
            '->match() build a set of MongoDB expressions in the query builder.'
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
        $decorator->match('dumb', $value);
    }

    public function addMatchedValue($value)
    {
        $query = $value->getQuery();
        foreach ($query['$'.$this->association] as $subValue) {
            $this->match[] = $subValue['dumb'];
        }
    }

    public function expr()
    {
        return new Expr('$');
    }
}