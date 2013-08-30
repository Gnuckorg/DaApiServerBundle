<?php

namespace Da\ApiServerBundle\Doctrine\MongoDB;

use Doctrine\MongoDB\Query\Expr;
use Da\ApiServerBundle\Model\AbstractQueryBuilderDecorator as BaseAbstractQueryBuilderDecorator;

/**
 * The abstract decorator class handling the decorator pattern
 * used for the query builder for MongoDB.
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
        $expr = $this->expr()->field($field);

        foreach ($chunks as $chunkExpr) {
            if (!($chunkExpr instanceof Expr)) {
                throw new \InvalidArgumentException('A chunck must be an instance of "Doctrine\MongoDB\Query\Expr".');
            }

            if ($association === BaseAbstractQueryBuilderDecorator::ASSOCIATION_OR) {
                $expr->addOr($chunkExpr);
            } else {
                $expr->addAnd($chunkExpr);
            }
        }

        $this->addAnd($expr);
    }

    /**
     * {@inheritdoc}
     */
    protected function build(array $arguments, $association)
    {
        $chunks = array();
        //$this->

        return $chunks;

        if ($association === AbstractQueryBuilderDecorator::ASSOCIATION_OR) {
            $this->addOr($this->expr()->field($this->currentField)->equals($arguments[0]));
        } else {
            $this->equals($arguments[0]);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createChunk(array $chunks, $field, $association)
    {
        return $this->expr()->field();
    }
}