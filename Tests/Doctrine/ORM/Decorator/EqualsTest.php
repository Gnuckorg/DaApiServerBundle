<?php

/*
 * This file is part of the Da Project.
 *
 * (c) Thomas Prelot <tprelot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Da\ApiServerBundle\Tests\Doctrine\ORM\Decorator;

use Doctrine\ORM\Query\Expr;
use Da\ApiServerBundle\Doctrine\ORM\ObjectRepository;
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

        $values[] = array('abc', array('value' => 'dumb = ?', 'parameters' => array('abc')));
        $values[] = array('=~~abd', array('value' => 'dumb = ?', 'parameters' => array('abd')));
        $values[] = array('abc~*~bda', array('value' => 'dumb = ? OR dumb = ?', 'parameters' => array('abc', 'bda')));
        $values[] = array('=~~abc~*~=~~bca', array('value' => 'dumb = ? OR dumb = ?', 'parameters' => array('abc', 'bca')));
        $values[] = array('=~~abc~-~=~~2~-~=~~234d_s_d', array('value' => 'dumb = ? AND dumb = ? AND dumb = ?', 'parameters' => array('abc', '2', '234d_s_d')));

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
        $decorator = $this->getMockBuilder('Da\ApiServerBundle\Doctrine\ORM\Decorator\Equals')
            ->disableOriginalConstructor()
            ->setMethods(array('where', 'getDQLPart', 'translate'))
            ->getMock()
        ;
        $decorator
            ->expects($this->any())
            ->method('where')
            ->will($this->returnCallback(array($this, 'addMatchedValue')))
        ;
        $decorator
            ->expects($this->any())
            ->method('getDQLPart')
            ->will($this->returnValue(null))
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
     * @covers Da\ApiServerBundle\Doctrine\ORM\Decorator\Equals::handle
     * @covers Da\ApiServerBundle\Doctrine\ORM\Decorator\Equals::check
     * @covers Da\ApiServerBundle\Doctrine\ORM\Decorator\Equals::build
     * @dataProvider getValue
     */
    public function testMatch($value, $expected)
    {
        $this->match = array();

        $decorator = $this->getDecoratorMock();
        $decorator->match('dumb', $value);

        $this->assertEquals($expected, $this->match, 
            '->match() build a set of sql where expressions in the query builder.'
        );
    }

    /**
     * @covers Da\ApiServerBundle\Model\AbstractQueryBuilderDecorator::match
     * @covers Da\ApiServerBundle\Model\AbstractQueryBuilderDecorator::parse
     * @covers Da\ApiServerBundle\Doctrine\ORM\Decorator\Equals::handle
     * @covers Da\ApiServerBundle\Doctrine\ORM\Decorator\Equals::check
     * @dataProvider getBadValue
     * @expectedException InvalidArgumentException
     */
    public function testMatchBadArguments($value)
    {
        $this->match = array();

        $decorator = $this->getDecoratorMock();
        $decorator->match('dumb', $value);
    }

    public function addMatchedValue($value, array $parameters)
    {
        $this->match = array(
            'value' => $value,
            'parameters' => $parameters
        );
    }
}