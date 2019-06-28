<?php

namespace AppBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Class PlusUserCollection
 *
 * @Annotation
 *
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class PlusUserCollection extends Constraint
{
    public $message = '"{{ string }}" should contains only valid user entity.';
}
