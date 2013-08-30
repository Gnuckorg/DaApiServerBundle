<?php

namespace Da\ApiServerBundle\Model;

/**
 * The interface that a class should implements to be
 * used as a query builder decorator.
 *
 * @author Thomas Prelot <tprelot@gmail.com>
 */
interface QueryBuilderDecoratorInterface
{
    /**
     * Interpret a value with the following syntax:
     *     - myvalue
     *     - !##myvalue
     *     - in##myvalue1##myvalue2##myvalue3
     *     - >##myminvalue&&<##mymaxvalue
     *     - =##myvalue1||=##myvalue2||=##myvalue3
     *     - >##myminvalue&&!##myvalue
     *
     * @param string  $field  The field name.
     * @param mixed   $value  The value.
     *
     * @return QueryBuilderDecoratorInterface This.
     */
    function match($field, $value);
}