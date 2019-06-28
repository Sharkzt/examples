<?php

namespace AppBundle\Validator;

use AppBundle\Entity\PlusUser;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class PlusUserCollectionValidator
 */
class PlusUserCollectionValidator extends ConstraintValidator
{
    /**
     * @param mixed      $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (null === $value) {
            return;
        }

        if (is_string($value)) {
            throw new UnexpectedTypeException($value, PlusUser::class);
        }

        foreach ($value as $user) {
            if (!($user instanceof PlusUser)) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ string }}', (string) $user instanceof \object ? get_class($user) : $user)
                    ->addViolation();
            }
        }
    }
}
