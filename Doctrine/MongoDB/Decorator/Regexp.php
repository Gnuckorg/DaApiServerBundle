<?php

namespace Da\ApiServerBundle\Doctrine\MongoDB\Decorator;

use Da\ApiServerBundle\Doctrine\MongoDB\AbstractQueryBuilderDecorator;

/**
 * Regexp operation decorator.
 *
 * @author Thomas Prelot <tprelot@gmail.com>
 */
class Regexp extends AbstractQueryBuilderDecorator
{
    /**
     * {@inheritdoc}
     */
    protected function handle($operation)
    {
        return ($operation === '@');
    }

    /**
     * {@inheritdoc}
     */
    protected function check(array $arguments)
    {
        $argumentsCount = count($arguments);

        if ($argumentsCount < 1) {
            throw new \InvalidArgumentException('The "regexp" method takes one argument.');
        } else if ($argumentsCount > 1) {
            throw new \InvalidArgumentException('Too many arguments for a "regexp" operation.');
        }

        return $arguments;
    }

    /**
     * {@inheritdoc}
     */
    protected function interpret(array $arguments, $field)
    {
        return $this->createChunk($field)->equals(new \MongoRegex($arguments[0]));
    }
}