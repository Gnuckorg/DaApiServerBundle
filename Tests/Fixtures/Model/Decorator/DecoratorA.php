<?php

namespace Da\ApiServerBundle\Tests\Fixtures\Model\Decorator;

use Da\ApiServerBundle\Model\AbstractQueryBuilderDecorator;

class DecoratorA extends AbstractQueryBuilderDecorator
{
	public function a()
    {
        return 'a';
    }

	/**
     * {@inheritdoc}
     */
    protected function handle($operation)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function check(array $arguments)
    {
        return $arguments;
    }

    /**
     * {@inheritdoc}
     */
    protected function build(array $arguments, $association)
    {
    }
}