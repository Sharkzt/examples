<?php

namespace AppBundle\Services\Reaction;

use AppBundle\Entity\PlusSpock;
use AppBundle\Entity\PlusThanksReaction;

/**
 * Class ThanksProducer
 */
class ThanksProducer
{
    /**
     * @return PlusSpock
     */
    public function create(): PlusSpock
    {
        return (new PlusThanksReaction())
            ->setSlug(PlusThanksReaction::SLUG);
    }

    /**
     * @param string $slug
     *
     * @return bool
     */
    public function isSupported(string $slug): bool
    {
        return PlusThanksReaction::SLUG === $slug;
    }
}
