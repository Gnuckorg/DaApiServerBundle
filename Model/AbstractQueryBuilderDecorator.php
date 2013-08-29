<?php

namespace Da\ApiServerBundle\Model;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Da\ApiServerBundle\Model\AbstractQueryBuilderDecorator;

/**
 * The abstract decorator class handling the decorator pattern
 * used for the query builder.
 *
 * @author Thomas Prelot <tprelot@gmail.com>
 */
abstract class AbstractQueryBuilderDecorator implements QueryBuilderDecoratorInterface
{
    const ASSOCIATION_AND = '&&';
    const ASSOCIATION_OR  = '||';

    /**
     * The decorated query builder.
     *
     * @var QueryBuilderDecoratorInterface
     */
    protected $decorated;

    /**
     * Constructor.
     *
     * @param QueryBuilderDecoratorInterface $decorated A decorated object. 
     *                                                  The real query builder cannot check the interface.
     */
    public function __construct($decorated)
    {
        $this->decorated = $decorated;
    }

    /**
     * Magic redirection to the decorated query builder.
     *
     * @param string $propertyName The name of the property.
     *
     * @return mixed The property values.
     */
    public function __get($propertyName)
    {
        return $this->decorated->$propertyName;
    }

    /**
     * Magic redirection to the decorated query builder.
     *
     * @param string $methodName The name of the method.
     * @param array  $arguments  The passed arguments.
     *
     * @return mixed The return of the method invoked on the decorated query builder.
     */
    public function __call($methodName, $arguments)
    {
        $class = new \ReflectionClass($this->decorated);
        
        try {
            $method = $class->getMethod($methodName);
        } catch (\ReflectionException $exception) {
            if ($this->decorated instanceof AbstractQueryBuilderDecorator) {
                $this->decorated->__call($methodName, $arguments);
            } else {
                throw $exception;
            }
        }

        return $method->invokeArgs($this->decorated, $arguments);
    }

    /**
     * {@inheritdoc}
     */
    public function match($value, $parsed = false)
    {
        if (!$parsed) {
            $value = $this->parse($value);
        }

        foreach ($value as $key => $operation) {
            if ($this->handle($operation['operation'])) {
                $arguments = $this->check($operation['arguments']);
                $this->build($operation['arguments'], $operation['association']);
                unset($value[$key]);
            }
        }

        if (!empty($value))
            $this->decorated->match($value, true);

        return $this;
    }

    /**
     * Parse a value with the following syntax:
     *     - myvalue
     *     - !##myvalue
     *     - in##myvalue1##myvalue2##myvalue3
     *     - >##myminvalue&&<##mymaxvalue
     *     - =##myvalue1||=##myvalue2||=##myvalue3
     *     - >##myminvalue&&!##myvalue
     *
     * @param string $value The non-parsed value.
     * 
     * @return array The parsed value.
     */
    protected function parse($value)
    {
        $operations = array();
        $association = self::ASSOCIATION_AND;

        $explodedValue = $explodedValueAnd = explode(self::ASSOCIATION_AND, $value);
        $explodedValueOr = explode(self::ASSOCIATION_OR, $value);

        if (count($explodedValueAnd) > 1 && count($explodedValueOr) > 1) {
            throw new \InvalidArgumentException('The syntax does not allow "&&" and "||" in the same expression for the moment.');
        } else if (count($explodedValueOr) > 1) {
            $association = self::ASSOCIATION_OR;
            $explodedValue = $explodedValueOr;
        }

        foreach ($explodedValue as $operationValue) {
            $explodedOperationValue = explode('##', $operationValue);
            if (count($explodedOperationValue) === 1) {
                    $operation = array(
                    'association' => $association,
                    'operation'   => '=',
                    'arguments'   => $explodedOperationValue
                );
            } else {
                $operation = array(
                    'association' => $association,
                    'operation'   => array_shift($explodedOperationValue),
                    'arguments'   => $explodedOperationValue
                );
            }

            $operations[] = $operation;
        }

        return $operations;
    }

    /**
     * Does this decorator handle that operation?
     * (used in a chain of responsibility pattern)
     *
     * @param string $operation The operation name.
     * 
     * @return boolean True if it handles that operation, false otherwise.
     */
    abstract protected function handle($operation);

    /**
     * Check and format the arguments.
     *
     * @param array $arguments The arguments.
     * 
     * @return array The formatted arguments
     *
     * @throws \InvalidArgumentException If the check fails.
     */
    abstract protected function check(array $arguments);

    /**
     * Build the interpreted value in the query builder.
     *
     * @param array  $arguments   The arguments.
     * @param string $association The kind of association with its predecessor.
     */
    abstract protected function build(array $arguments, $association);
}