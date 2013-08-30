<?php

namespace Da\ApiServerBundle\Doctrine\ORM;

use Da\ApiServerBundle\Model\AbstractQueryBuilderDecorator as BaseAbstractQueryBuilderDecorator;

/**
 * The abstract decorator class handling the decorator pattern
 * used for the query builder for relational databases.
 *
 * @author Thomas Prelot <tprelot@gmail.com>
 */
abstract class AbstractQueryBuilderDecorator implements BaseAbstractQueryBuilderDecorator
{
    /**
     * {@inheritdoc}
     */
    protected function assemble(array $chunks, $field, $association)
    {
    	$where = '';
    	$associationWord = '';
    	$parameters = array();

    	foreach ($chunks as $chunk) {
            $parameters = array_merge($parameters, $chunk['parameters']);

            $where .= sprintf(
            	' %s %s = %s ',
            	$associationWord,
            	$field,
            	$chunk['value']
            );

            if (empty($associationWord)) {
	            if ($association === BaseAbstractQueryBuilderDecorator::ASSOCIATION_OR) {
	                $associationWord = 'OR';
	            } else {
	                $associationWord = 'AND';
	            }
	        }
        }

    	if (null === $this->getDQLPart('where')) {
            $this->where($where, $parameters);
        } else {
            $this->andWhere($where, $parameters);
        }
    }
}