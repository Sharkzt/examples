<?php

namespace FollowersBundle\Services\Handlers;

use AppBundle\Entity\PlusEmployee;

/**
 * Class FollowedEmailHandler
 *
 * @deprecated
 */
class FollowedEmailHandler extends AbstractEmailHandler
{
    /**
     * @param PlusEmployee $employee
     * @param PlusEmployee $featuredEmployee
     *
     * @return AbstractEmailHandler
     */
    public function handle(PlusEmployee $employee, PlusEmployee $featuredEmployee): AbstractEmailHandler
    {
        if (!$this->isEmailReceiver($employee)) {
            return $this;
        }

        $parsedContent = $this
            ->getContentHandler()
            ->handle($employee, $featuredEmployee)
            ->getParsedContent();

        $this
            ->getSendingContext()
            ->setBody($parsedContent->getBody())
            ->setSubject($parsedContent->getSubject())
            ->setHeaders([])
            ->addTag('followed_newFollower')
            ->addTag('followed_newFollower_'.$this->getUrlHandler()->getCurrentSite())
            ->setSlug('follower_email');

        $this->getSender()->sendEmailByEmployeeAndUser($employee, $employee->getUser());

        return $this;
    }

    /**
     * @param PlusEmployee $employee
     *
     * @return bool
     */
    public function isEmailReceiver(PlusEmployee $employee): bool
    {
        return $this->getSettings()->isFollowEmailReceiver($employee);
    }
}
