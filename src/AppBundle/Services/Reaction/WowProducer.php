<?php

namespace AppBundle\Services\Reaction;

use AppBundle\Entity\PlusSpock;
use AppBundle\Entity\PlusWowReaction;

/**
 * Class WowProducer
 */
class WowProducer implements ProduceInterface
{
    /**
     * @return PlusSpock
     */
    public function create(): PlusSpock
    {
        return (new PlusWowReaction())
            ->setSlug(PlusWowReaction::SLUG);
    }

    /**
     * @param string $slug
     *
     * @return bool
     */
    public function isSupported(string $slug): bool
    {
        return PlusWowReaction::SLUG === $slug;
    }
}
