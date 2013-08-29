<?php

namespace Da\ApiServerBundle\Doctrine\MongoDB\Decorator;

use Da\ApiServerBundle\Model\AbstractQueryBuilderDecorator;

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
        $where = sprintf(
            %s = %s,
            $field,
            $arguments[0]
        );
        if (null === $this->getDQLPart('where')) {
            $this->where($where);
        } else if ($association === AbstractQueryBuilderDecorator::ASSOCIATION_OR) {
            $this->orWhere($where);
        } else {
            $this->andWhere($where);
        }
    }
}