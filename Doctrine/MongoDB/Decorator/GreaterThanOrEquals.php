<?php

namespace Da\ApiServerBundle\Doctrine\MongoDB\Decorator;

use Da\ApiServerBundle\Doctrine\MongoDB\AbstractQueryBuilderDecorator;

/**
 * Greater than or equals operation decorator.
 *
 * @author Thomas Prelot <tprelot@gmail.com>
 */
class GreaterThanOrEquals extends AbstractQueryBuilderDecorator
{
    /**
     * {@inheritdoc}
     */
    protected function handle($operation)
    {
        return ($operation === '>=');
    }

    /**
     * {@inheritdoc}
     */
    protected function check(array $arguments)
    {
        $argumentsCount = count($arguments);

        if ($argumentsCount < 1) {
            throw new \InvalidArgumentException('The "greater than or equals" method take one argument.');
        } else if ($argumentsCount > 1) {
            throw new \InvalidArgumentException('Too many arguments for an "greater than or equals" operation.');
        }

        return $arguments;
    }

    /**
     * {@inheritdoc}
     */
    protected function interpret(array $arguments, $field)
    {
        return $this->createChunk($field)->gte($arguments[0]);
    }
}