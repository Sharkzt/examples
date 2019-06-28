<?php

namespace AppBundle\Security\Guard;

use AppBundle\Entity\PlusUser;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Guard\JWTTokenAuthenticator as BaseAuthenticator;
use Symfony\Component\Security\Core\User\UserInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\InvalidTokenException;

/**
 * Class JWTTokenAuthenticator
 */
class JWTTokenAuthenticator extends BaseAuthenticator
{
    /**
     * {@inheritdoc}
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        /** @var PlusUser $user */
        if (!$user->isActive()) {
            throw new InvalidTokenException(sprintf('User is deactivated'));
        }

        return true;
    }
}
