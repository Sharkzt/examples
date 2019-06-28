<?php

namespace FollowersBundle\Event;

use AppBundle\Entity\PlusEmployeeFollower;
use AppBundle\Entity\PlusEmployee;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class FollowersEvents
 */
class FollowersEvent extends Event
{
    const ON_FOLLOW = 'followers.on_follow';
    const POST_FOLLOW = 'followers.post_follow';
    const ON_UNFOLLOW = 'followers.on_unfollow';

    /**
     * @var PlusEmployee
     */
    private $employee;

    /**
     * @var PlusEmployee
     */
    private $followedEmployee;

    /**
     * @var PlusEmployeeFollower
     */
    private $plusFollowers;

    /**
     * @param PlusEmployee $employee
     *
     * @return FollowersEvent
     */
    public function setEmployee(PlusEmployee $employee): FollowersEvent
    {
        $this->employee = $employee;

        return $this;
    }

    /**
     * @return PlusEmployee
     */
    public function getEmployee(): PlusEmployee
    {
        return $this->employee;
    }

    /**
     * @param PlusEmployee $followedEmployee
     *
     * @return FollowersEvent
     */
    public function setFollowedEmployee(PlusEmployee $followedEmployee): FollowersEvent
    {
        $this->followedEmployee = $followedEmployee;

        return $this;
    }

    /**
     * @return PlusEmployee
     */
    public function getFollowedEmployee(): PlusEmployee
    {
        return $this->followedEmployee;
    }

    /**
     * @return PlusEmployeeFollower
     */
    public function getPlusFollowers(): PlusEmployeeFollower
    {
        return $this->plusFollowers;
    }

    /**
     * @param PlusEmployeeFollower $plusFollowers
     * @return FollowersEvent
     */
    public function setPlusFollowers(PlusEmployeeFollower $plusFollowers): FollowersEvent
    {
        $this->plusFollowers = $plusFollowers;

        return $this;
    }
}
