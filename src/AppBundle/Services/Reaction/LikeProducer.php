<?php

namespace AppBundle\Services\Reaction;

use AppBundle\Entity\PlusLikeReaction;
use AppBundle\Entity\PlusSpock;

/**
 * Class LikeProducer
 */
class LikeProducer implements ProduceInterface
{
    /**
     * @return PlusSpock
     */
    public function create(): PlusSpock
    {
        return (new PlusLikeReaction())
            ->setSlug(PlusLikeReaction::SLUG);
    }

    /**
     * @param string $slug
     *
     * @return bool
     */
    public function isSupported(string $slug): bool
    {
        return PlusLikeReaction::SLUG === $slug;
    }
}
