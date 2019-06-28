<?php

namespace AppBundle\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

class ObjectHasOwnerVoter extends Voter
{

    const VIEW = 'view';
    const EDIT = 'edit';

    /**
     * @var AccessDecisionManagerInterface
     */
    private $decisionManager;

    /**
     * @param AccessDecisionManagerInterface $decisionManager
     *
     * @return $this;
     */
    public function setDecisionManager($decisionManager)
    {
        $this->decisionManager = $decisionManager;

        return $this;
    }

    /**
     * @param string                  $attribute
     * @param ObjectHasOwnerInterface $subject
     *
     * @return bool
     */
    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, array(self::VIEW, self::EDIT))) {
            return false;
        }

        if (!($subject instanceof ObjectHasOwnerInterface)) {
            return false;
        }

        return true;
    }

    /**
     * @param string                  $attribute
     * @param ObjectHasOwnerInterface $subject
     * @param TokenInterface          $token
     *
     * @return boolean
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if (!($subject instanceof ObjectHasOwnerInterface)) {
            return false;
        }

        if ($this->decisionManager->decide($token, ['ROLE_ADMINISTRATOR'])) {
            return true;
        }

        if (null === $subject->getOwner()) {
            return false;
        }

        return \spl_object_hash($subject->getOwner()) === \spl_object_hash($token->getUser());
    }

}
