<?php


namespace AppBundle\Listener;

use \Gedmo\SoftDeleteable\SoftDeleteableListener as BaseSoftDeleteableListener;
use Doctrine\Common\EventArgs;

class SoftDeleteableListner extends BaseSoftDeleteableListener
{
    /**
     * @inheritdoc
     */
    public function onFlush(EventArgs $args)
    {
        $ea = $this->getEventAdapter($args);
        $om = $ea->getObjectManager();

        if (false === $om->getFilters()->isEnabled('softdeleteable')) {
            return;
        }

        parent::onFlush($args);
    }
}
