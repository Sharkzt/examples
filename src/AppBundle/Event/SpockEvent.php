<?php


namespace AppBundle\Event;

use AppBundle\Entity\PlusSpock;
use Symfony\Component\EventDispatcher\Event;

class SpockEvent extends Event
{

    const SPOCK_POST_CREATE = 'spock.post_create';
    const SPOCK_POST_DELETE = 'spock.post_delete';

    /**
     * @var PlusSpock
     */
    private $spock;

    /**
     * @return PlusSpock
     */
    public function getSpock(): PlusSpock
    {
        return $this->spock;
    }

    /**
     * @param PlusSpock $spock
     *
     * @return SpockEvent
     */
    public function setSpock(PlusSpock $spock): SpockEvent
    {
        $this->spock = $spock;

        return $this;
    }

}
