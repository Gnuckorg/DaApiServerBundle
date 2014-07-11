<?php

namespace Da\ApiServerBundle\Doctrine\ORM;

use Doctrine\DBAL\Types\Type;
use Da\ApiServerBundle\Model\AbstractQueryBuilderDecorator as BaseAbstractQueryBuilderDecorator;
use Da\ApiServerBundle\Exception\InvalidFieldValueException;

/**
 * The abstract decorator class handling the decorator pattern
 * used for the query builder for relational databases.
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
        $where = '';
        $associationWord = '';
        $parameters = array();

        foreach ($chunks as $chunk) {
            $parameters = array_merge($parameters, $chunk['parameters']);

            if (!empty($associationWord)) {
                $where .= sprintf(
                    '%s %s',
                    $associationWord,
                    $chunk['value']
                );
            } else {
                $where .= $chunk['value'];
            }

            if (empty($associationWord)) {
                if ($association === BaseAbstractQueryBuilderDecorator::ASSOCIATION_OR) {
                    $associationWord = ' OR';
                } else {
                    $associationWord = ' AND';
                }
            }
        }

        if (null === $this->getDQLPart('where')) {
            $this->where($where, $parameters);
        } else {
            $this->andWhere($where, $parameters);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function translate(array $arguments, $field)
    {
        foreach ($arguments as $index => $argument) {
            $fieldType = Type::getType($this->fieldTypes[$field]);

            switch ($fieldType->getName()) {
                case Type::DATE:
                case Type::DATETIME:
                    try {
                        $argument = \DateTime::createFromFormat(\DateTime::ISO8601, $argument);
                    } catch (Exception $e) {
                        try {
                            $argument = new \DateTime($argument);
                        } catch (Exception $e) {
                            throw new InvalidFieldValueException($field, $argument);
                        }
                    }
                    $arguments[$index] = $argument;
                    break;
                default:
                    $platform = $this->getEntityManager()->getConnection()->getDatabasePlatform();
                    $arguments[$index] = $fieldType->convertToPHPValue($argument, $platform);
                    break;
            }
        }

        return $arguments;
    }

    /**
     * {@inheritdoc}
     */
    protected function checkChunk($chunk)
    {
        if (!is_array($chunk) || !is_array($chunk['parameters']) || !isset($chunk['value'])) {
            throw new \InvalidArgumentException('The interpret method of a decorator must return an associative array with a "value" and a "parameters" key or an array of this kind of associative arrays.');
        }
    }

    /**
     * Create an initialized chunk.
     *
     * @param string       $field    The field name.
     * @param string       $operator The operator.
     * @param string|array $value    The value.
     *
     * @return An initilized empty expression.
     */
    protected function createChunk($field, $operator, $value)
    {
        if (!is_array($value)) {
            $parameters = array($value);
            $value = '?';
        } else {
            $parameters = $value;
            $value = sprintf(
                '(%s)',
                implode(',', $parameters)
            );
        }

        return array(
            'value' => sprintf(
                '%s %s %s',
                $field,
                $operator,
                $value
            ),
            'parameters' => $parameters
        );
    }
}