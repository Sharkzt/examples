<?php

namespace FollowersBundle\Event\Listener;

use FollowersBundle\Event\FollowersEvent;
use FollowersBundle\Services\Followers\FollowersCreator;
use FollowersBundle\Services\Followers\FollowersDestroyer;
use FollowersBundle\Services\Handlers\AbstractEmailHandler;
use UserManagementBundle\Event\FollowUserEvent;

/**
 * Class FollowEmployee
 */
class FollowEmployeeListener
{
    /**
     * @var FollowersCreator
     */
    private $creator;

    /**
     * @var FollowersDestroyer
     */
    private $destroyer;

    /**
     * @var AbstractEmailHandler
     */
    private $emailHandler;

    /**
     * @param FollowersCreator $creator
     *
     * @return FollowEmployeeListener
     */
    public function setFollowerCreator(FollowersCreator $creator): FollowEmployeeListener
    {
        $this->creator = $creator;

        return $this;
    }

    /**
     * @return FollowersCreator
     */
    public function getFollowerCreator(): FollowersCreator
    {
        return $this->creator;
    }

    /**
     * @return FollowersDestroyer
     */
    public function getDestroyer(): FollowersDestroyer
    {
        return $this->destroyer;
    }

    /**
     * @param FollowersDestroyer $destroyer
     *
     * @return FollowEmployeeListener
     */
    public function setDestroyer(FollowersDestroyer $destroyer): FollowEmployeeListener
    {
        $this->destroyer = $destroyer;

        return $this;
    }

    /**
     * @return AbstractEmailHandler
     */
    public function getEmailHandler(): AbstractEmailHandler
    {
        return $this->emailHandler;
    }

    /**
     * @param AbstractEmailHandler $emailHandler
     *
     * @return FollowEmployeeListener
     */
    public function setEmailHandler(AbstractEmailHandler $emailHandler): FollowEmployeeListener
    {
        $this->emailHandler = $emailHandler;

        return $this;
    }

    /**
     * Event name - "user_management.user_followed"
     *
     * @param FollowUserEvent $event
     */
    public function onUserFollowed(FollowUserEvent $event)
    {
        $this->getEmailHandler()->handle(
            $event->getFollowee()->getCurrentEmployee(),
            $event->getFollower()->getCurrentEmployee()
        );
    }

    /**
     * @param FollowersEvent $event
     * @return FollowersCreator
     *
     * @deprecated
     */
    public function follow(FollowersEvent $event): FollowersCreator
    {
        return $this->getFollowerCreator()->follow($event->getEmployee(), $event->getFollowedEmployee());
    }

    /**
     * @param FollowersEvent $event
     * @return FollowersDestroyer
     *
     * @deprecated
     */
    public function unFollow(FollowersEvent $event): FollowersDestroyer
    {
        return $this->getDestroyer()->unFollow($event->getPlusFollowers());
    }

    /**
     * @param FollowersEvent $event
     * @return AbstractEmailHandler
     *
     * @deprecated
     */
    public function sendFollowEmail(FollowersEvent $event): AbstractEmailHandler
    {
        return $this->getEmailHandler()->handle($event->getFollowedEmployee(), $event->getEmployee());
    }
}
