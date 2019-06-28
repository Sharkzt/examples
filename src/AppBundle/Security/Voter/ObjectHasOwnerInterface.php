<?php


namespace AppBundle\Security\Voter;

use AppBundle\Entity\PlusUser;

interface ObjectHasOwnerInterface
{

    /**
     * @return PlusUser
     */
    public function getOwner();

}
