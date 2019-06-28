<?php

namespace AppBundle\Services\Reaction;

use AppBundle\Entity\PlusKeepItUpReaction;
use AppBundle\Entity\PlusSpock;

/**
 * Class KeepItUpProducer
 */
class KeepItUpProducer implements ProduceInterface
{
    /**
     * @return PlusSpock
     */
    public function create(): PlusSpock
    {
        return (new PlusKeepItUpReaction())
            ->setSlug(PlusKeepItUpReaction::SLUG);
    }

    /**
     * @param string $slug
     *
     * @return bool
     */
    public function isSupported(string $slug): bool
    {
        return PlusKeepItUpReaction::SLUG === $slug;
    }
}
