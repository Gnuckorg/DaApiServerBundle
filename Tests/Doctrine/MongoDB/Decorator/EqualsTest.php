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

        $values[] = array('abc', 'abc');
        $values[] = array('=##abd', 'abd');
        //$values[] = array('=##abc||=##bca');
        //$values[] = array('=##abc&&=##2&&=##234d_s_d');
        // TODO: test bad values.

        return $values;
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
        $this->setMatchedValue();

        $decorator = $this->getMockBuilder('Da\ApiServerBundle\Doctrine\MongoDB\Decorator\Equals')
            ->disableOriginalConstructor()
            ->setMethods(array('equals'))
            ->getMock()
        ;
        $decorator
            ->expects($this->any())
            ->method('equals')
            ->will($this->returnCallback(array($this, 'setMatchedValue')))
        ;

        $decorator->match($value);
        $this->assertEquals($expected, $this->match, 
            '->match() redirect the value in the equals method.'
        );
    }

    public function setMatchedValue($value = '')
    {
        $this->match = $value;
    }
}