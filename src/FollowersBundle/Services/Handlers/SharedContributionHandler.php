<?php

namespace FollowersBundle\Services\Handlers;

use AppBundle\Entity\PlusContribution;
use AppBundle\Entity\PlusEmployeeFollower;
use Doctrine\ORM\EntityNotFoundException;

/**
 * Class SharedContributionHandler
 *
 * @deprecated
 */
class SharedContributionHandler
{
    /**
     * @var ContributionEmailHandler
     */
    private $emailHandler;

    /**
     * @return ContributionEmailHandler
     */
    public function getEmailHandler(): ContributionEmailHandler
    {
        return $this->emailHandler;
    }

    /**
     * @param ContributionEmailHandler $emailHandler
     * @return SharedContributionHandler
     */
    public function setEmailHandler(ContributionEmailHandler $emailHandler): SharedContributionHandler
    {
        $this->emailHandler = $emailHandler;

        return $this;
    }

    /**
     * @param PlusContribution $contribution
     *
     * @return SharedContributionHandler
     */
    public function sendEmails(PlusContribution $contribution): SharedContributionHandler
    {
        if (!$contribution->getIsShared()) {
            return $this;
        }

        $employee = $contribution->getEmployee();
        foreach ($employee->getUser()->getFollowers() as $follower) {
            try {
                $this->emailHandler->handle($follower->getCurrentEmployee(), $employee);
            } catch (EntityNotFoundException $e) {
                // hotfix of SA-3021
            }
        }

        return $this;
    }
}
