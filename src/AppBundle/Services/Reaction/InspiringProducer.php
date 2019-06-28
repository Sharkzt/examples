<?php

namespace AppBundle\Services\Reaction;

use AppBundle\Entity\PlusInspiringReaction;
use AppBundle\Entity\PlusSpock;

/**
 * Class InspiringProducer
 */
class InspiringProducer implements ProduceInterface
{
    /**
     * @return PlusSpock
     */
    public function create(): PlusSpock
    {
        return (new PlusInspiringReaction())
            ->setSlug(PlusInspiringReaction::SLUG);
    }

    /**
     * @param string $slug
     *
     * @return bool
     */
    public function isSupported(string $slug): bool
    {
        return PlusInspiringReaction::SLUG === $slug;
    }
}
