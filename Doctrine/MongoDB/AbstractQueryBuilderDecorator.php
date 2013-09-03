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
abstract class AbstractQueryBuilderDecorator extends BaseAbstractQueryBuilderDecorator
{
    /**
     * {@inheritdoc}
     */
    protected function assemble(array $chunks, $field, $association)
    {
        $expr = $this->expr()->field($field);

        foreach ($chunks as $chunkExpr) {
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
    protected function checkChunk($chunk)
    {
        if (!($chunk instanceof Expr)) {
            throw new \InvalidArgumentException('The interpret method of a decorator must return an instance of "Doctrine\MongoDB\Query\Expr" or an array of "Doctrine\MongoDB\Query\Expr".');
        }
    }

    /**
     * Create an initialized chunk.
     *
     * @param string $field The field name.
     *
     * @return An initilized empty expression.
     */
    protected function createChunk($field)
    {
        return $this->expr()->field($field);
    }
}