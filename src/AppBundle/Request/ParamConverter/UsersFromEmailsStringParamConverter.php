<?php

namespace AppBundle\Request\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use UserManagementBundle\Services\Parser\UsersFromStringParser;

/**
 * Class UsersFromEmailsStringParamConverter
 */
class UsersFromEmailsStringParamConverter implements ParamConverterInterface
{
    /**
     * @var UsersFromStringParser
     */
    private $usersFromString;

    /**
     * @return UsersFromStringParser
     */
    public function getUsersFromString(): UsersFromStringParser
    {
        return $this->usersFromString;
    }

    /**
     * @param UsersFromStringParser $usersFromString
     *
     * @return UsersFromEmailsStringParamConverter
     */
    public function setUsersFromString(UsersFromStringParser $usersFromString): UsersFromEmailsStringParamConverter
    {
        $this->usersFromString = $usersFromString;

        return $this;
    }

    /**
     * @param ParamConverter $configuration
     *
     * @return bool
     */
    public function supports(ParamConverter $configuration)
    {
        return $configuration->getName() === 'users';
    }

    /**
     * @param Request        $request
     * @param ParamConverter $configuration
     *
     * @return bool|void
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $users  = $request->attributes->get('users');

        if (!$users) {
            throw new \InvalidArgumentException(sprintf('Users attribute is missing'));
        }

        $users = $this->getUsersFromString()
            ->parse($users)
            ->getUsers()
        ;

        $request->attributes->set($configuration->getName(), $users);
    }
}
