<?php

namespace Da\ApiServerBundle\Model;

/**
 * The interface that a class should implements to be
 * used as an object repository with a decorated query builder.
 *
 * @author Thomas Prelot <tprelot@gmail.com>
 */
interface ObjectRepositoryInterface
{
    /**
     * Get the class name of the decorators.
     *
     * @return array MongoDBQueryBuilderDecorator
     */
    static function getDecorators();

    /**
     * Add a decorator.
     *
     * @param string The class name of the decorator.
     */
    static function addDecoratorClassName($decoratorClassName);
}