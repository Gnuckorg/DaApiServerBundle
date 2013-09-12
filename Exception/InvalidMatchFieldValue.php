<?php

namespace Da\ApiServerBundle\Exception;

/**
 * Exception thrown when a value of a field is bad. 
 *
 * @author Thomas Prelot
 */
class InvalidFieldValueException extends \InvalidArgumentException
{
	/**
     * Constructor
     *
     * @param string $fieldName  The name of the field.
     * @param string $fieldValue The value of the field.
     */
    public function __construct($fieldName, $fieldValue)
    {
        parent::__construct(
        	sprintf(
        		'The value "%s" is not valid for the field "%s".',
        		$fieldName,
        		$fieldValue 
        	)
        );
    }
}