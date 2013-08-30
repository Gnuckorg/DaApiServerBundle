<?php

namespace Da\ApiServerBundle\Doctrine\MongoDB\Decorator;

use Da\ApiServerBundle\Doctrine\MongoDB\AbstractQueryBuilderDecorator;

/**
 * Equals operation decorator.
 *
 * @author Thomas Prelot <tprelot@gmail.com>
 */
class Equals extends AbstractQueryBuilderDecorator
{
    /**
     * {@inheritdoc}
     */
    protected function handle($operation)
    {
        return ($operation === '=');
    }

    /**
     * {@inheritdoc}
     */
    protected function check(array $arguments)
    {
        $argumentsCount = count($arguments);
        if ($argumentsCount < 1) {
            throw new \InvalidArgumentException('The "equal" method take one argument.');
        } else if ($argumentsCount > 1) {
            throw new \InvalidArgumentException('Too many arguments for an "equal" operation.');
        }

        return $arguments;
    }

    /**
     * {@inheritdoc}
     */
    protected function build(array $arguments, $association)
    {
        if ($association === AbstractQueryBuilderDecorator::ASSOCIATION_OR) {
            $this->addOr($this->expr()->field($this->currentField)->equals($arguments[0]));
        } else {
            $this->equals($arguments[0]);
        }
    }
}