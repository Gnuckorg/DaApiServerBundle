<?php

namespace Da\ApiServerBundle\Tests\Fixtures\Model\Decorator;

use Da\ApiServerBundle\Model\AbstractQueryBuilderDecorator;

class DecoratorB extends AbstractQueryBuilderDecorator
{
	public function b()
    {
        return 'b';
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
    protected function interpret(array $arguments, $association)
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    protected function assemble(array $chunks, $field, $association)
    {
    }

    /**
     * {@inheritdoc}
     */
    protected function checkChunk($chunk)
    {
    }
}