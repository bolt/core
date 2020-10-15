<?php

declare(strict_types=1);

namespace Bolt\Validator;

use Bolt\Entity\Content;

interface ContentValidatorInterface
{
    /**
     * Validates the content provided, returns a list of constraint violations if there are any.
     *
     * This function is modeled on the ValidatorInterface->validate() that is part of the symfony/validation package.
     * The intention is to support Symfony Validation, but not require it.
     *
     * The function should return an array-like structure, for example ConstraintViolationListInterface, an array, or
     * a class implementing \Traversable, \Countable, \ArrayAccess. Basically anything that will allow php to show
     * the violations to the user.
     *
     * The items in the array should follow the format used by ConstraintViolationInterface from the symfony/validator
     * package - in the form of an associative array, class with public members, or class with getters.
     *
     * How to validate the content is up to the implementer of the interface.
     *
     * @param Content $content main content to validate
     *
     * returns array|ConstraintViolationListInterface or array-like structure with constraint violations
     */
    public function validate(Content $content);
}
