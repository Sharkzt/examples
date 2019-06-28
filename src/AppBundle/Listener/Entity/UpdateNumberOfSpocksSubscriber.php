<?php


namespace AppBundle\Listener\Entity;

use AppBundle\Entity\PlusContribution;
use AppBundle\Entity\PlusSpock;
use AppBundle\Event\SpockEvent;
use AppBundle\Event\UserManagerEvent;
use AppBundle\Repository\ContributionRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UpdateNumberOfSpocksSubscriber implements EventSubscriberInterface
{

    /**
     * @var ContributionRepository
     */
    private $repository;

    /**
     * @param ContributionRepository $repository
     *
     * @return UpdateNumberOfSpocksSubscriber
     */
    public function setRepository(ContributionRepository $repository): UpdateNumberOfSpocksSubscriber
    {
        $this->repository = $repository;

        return $this;
    }

    /**
     * @return array
     */
    static public function getSubscribedEvents()
    {
        return [
            SpockEvent::SPOCK_POST_CREATE => 'onSpockCreateDelete',
            SpockEvent::SPOCK_POST_DELETE => 'onSpockCreateDelete',
            UserManagerEvent::AFTER_USER_DELETE => 'afterUserDelete'
        ];
    }

    /**
     * @param SpockEvent $event
     */
    public function onSpockCreateDelete(SpockEvent $event)
    {
        $contribution = $event->getSpock()->getContribution();

        $this->updateAllNumbers($contribution, $event->getSpock());
    }

    /**
     * @param UserManagerEvent $event
     */
    public function afterUserDelete(UserManagerEvent $event)
    {
        $user = $event->getUser();

        foreach ($user->getSpocks() as $spock) {
            $this->updateAllNumbers($spock->getContribution(), $spock);
        }
    }

    /**
     * @param PlusContribution $contribution
     * @param PlusSpock        $spock
     *
     * @return void
     */
    private function updateAllNumbers(PlusContribution $contribution, PlusSpock $spock): void
    {
        $this->repository
            //numberOfSpocks is total number, force it's update
            ->updateLikesNumberOfSomething(PlusSpock::NUMBER_OF_FIELD, $contribution, new PlusSpock())
            //also update certain type of deleted/created like
            ->updateLikesNumberOfSomething($spock::NUMBER_OF_FIELD, $contribution, $spock)
        ;
    }
}
