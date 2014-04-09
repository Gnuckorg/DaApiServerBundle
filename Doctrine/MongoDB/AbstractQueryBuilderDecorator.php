<?php

namespace Da\ApiServerBundle\Doctrine\MongoDB;

use Doctrine\MongoDB\Query\Expr;
use Doctrine\ODM\MongoDB\Types\Type;
use Doctrine\ODM\MongoDB\Types\DateType;
use Da\ApiServerBundle\Model\AbstractQueryBuilderDecorator as BaseAbstractQueryBuilderDecorator;
use Da\ApiServerBundle\Exception\InvalidFieldValueException;

/**
 * The abstract decorator class handling the decorator pattern
 * used for the query builder for MongoDB.
 *
 * @author Thomas Prelot <tprelot@gmail.com>
 */
abstract class AbstractQueryBuilderDecorator extends BaseAbstractQueryBuilderDecorator
{
    /**
     * Wheter or not it is a OR request.
     *
     * @var boolean
     */
    protected $isOrRequest = false;

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

        // Double OR queries are ok but triple OR queries are not performant (bad mongodb optimization).
        if ($association === BaseAbstractQueryBuilderDecorator::ASSOCIATION_OR && $this->isOrRequest) {
            $expr = $this->expr()->field($field)->addAnd($expr);
        }

        $this->isOrRequest = true;

        $query = array_merge_recursive($this->getQueryArray(), $expr->getQuery());
        $this->setQueryArray($query);
    }

    /**
     * {@inheritdoc}
     */
    protected function translate(array $arguments, $field)
    {
        foreach ($arguments as $index => $argument) {
            $fieldType = Type::getType($this->fieldTypes[$field]);

            if ($fieldType instanceof DateType) {
                try {
                    $argument = \DateTime::createFromFormat('Y-m-d\TH:i:s\Z', $argument);
                } catch (Exception $e) {
                    try {
                        $argument = new \DateTime($argument);
                    } catch (Exception $e) {
                        throw new InvalidFieldValueException($field, $argument);
                    }
                }
                $arguments[$index] = $argument;
            } else {
                $arguments[$index] = $fieldType->convertToPHPValue($argument);
            }
        }

        return $arguments;
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